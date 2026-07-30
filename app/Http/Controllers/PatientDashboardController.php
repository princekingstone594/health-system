<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\FollowUp;
use App\Services\AiFollowUpService;

class PatientDashboardController extends Controller
{
    protected $followUpService;

    // ✅ Inject AI service
    public function __construct(AiFollowUpService $followUpService)
    {
        $this->followUpService = $followUpService;
    }

    public function index()
    {
        $user = Auth::user();

        // 🔍 Get patient profile
        $patient = Patient::where('patient_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('dashboard')
                ->with('error', 'Patient profile not found.');
        }

        // 📅 Upcoming appointments
        $upcomingAppointments = Appointment::where('patient_id', $patient->id)
            ->where('appointment_date', '>=', now())
            ->with('doctor')
            ->orderBy('appointment_date', 'asc')
            ->get();

        // 🕘 Past appointments
        $pastAppointments = Appointment::where('patient_id', $patient->id)
            ->where('appointment_date', '<', now())
            ->with('doctor')
            ->orderBy('appointment_date', 'desc')
            ->get();

        // 📊 Stats
        $totalAppointments = Appointment::where('patient_id', $patient->id)->count();

        $upcomingCount = Appointment::where('patient_id', $patient->id)
            ->where('appointment_date', '>=', now())
            ->count();

        // 🤖 STEP 5 — AUTO AI FOLLOW-UP (optional smart trigger)
        $this->autoTriggerFollowUp($patient);

        // 📬 Get follow-ups
        $followUps = FollowUp::where('patient_id', $patient->id)
            ->latest()
            ->get();

        return view('patient.dashboard', compact(
            'patient',
            'upcomingAppointments',
            'pastAppointments',
            'totalAppointments',
            'upcomingCount',
            'followUps'
        ));
    }

    // 🤖 MANUAL TRIGGER (button click)
    public function triggerFollowUp()
    {
        $user = auth()->user();

        $patient = Patient::where('patient_id', $user->id)->first();

        if (!$patient) {
            return back()->with('error', 'Patient not found.');
        }

        $this->followUpService->createAutoFollowUp($patient, [
            'condition' => 'general checkup'
        ]);

        return back()->with('success', 'AI Follow-Up sent!');
    }

    // 🤖 STEP 5 — AUTO TRIGGER LOGIC
    private function autoTriggerFollowUp($patient)
    {
        // 🕒 Check last follow-up (avoid spam)
        $lastFollowUp = FollowUp::where('patient_id', $patient->id)
            ->latest()
            ->first();

        if ($lastFollowUp && $lastFollowUp->created_at->diffInHours(now()) < 24) {
            return; // ❌ Don't send too frequently
        }

        // 📅 Check last appointment
        $lastAppointment = Appointment::where('patient_id', $patient->id)
            ->latest('appointment_date')
            ->first();

        if (!$lastAppointment) {
            return;
        }

        // ⏳ Trigger if appointment was 1–3 days ago
        if (
            $lastAppointment->appointment_date->diffInDays(now()) >= 1 &&
            $lastAppointment->appointment_date->diffInDays(now()) <= 3
        ) {
            $this->followUpService->createAutoFollowUp($patient, [
                'condition' => 'post-appointment checkup'
            ]);
        }
    }
}