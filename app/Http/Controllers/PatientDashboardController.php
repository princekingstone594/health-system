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
        $this->authorizeRole('patient');

        $userId = auth()->id();
        $user = Auth::user();

        // 🔍 Get patient profile
        $patient = Patient::where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('dashboard')
                ->with('error', 'Patient profile not found.');
        }

        // 📅 Upcoming appointments (for this user)
        $upcomingAppointments = Appointment::where('user_id', $userId)
            ->where('appointment_date', '>=', now())
            ->with('doctor')
            ->orderBy('appointment_date', 'asc')
            ->get();

        // 🕘 Past appointments
        $pastAppointments = Appointment::where('user_id', $userId)
            ->where('appointment_date', '<', now())
            ->with('doctor')
            ->orderBy('appointment_date', 'desc')
            ->get();

        // 📊 Stats
        $totalAppointments = Appointment::where('user_id', $userId)->count();

        $upcomingCount = Appointment::where('user_id', $userId)
            ->where('appointment_date', '>=', now())
            ->count();

        // 🤖 STEP 5 — AUTO AI FOLLOW-UP (optional smart trigger)
        $this->autoTriggerFollowUp($patient);

        // 📬 Get follow-ups (for this user as patient)
        $followUps = FollowUp::where('user_id', $userId)
            ->latest()
            ->get();

        return view('patients.dashboard', compact(
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

        $patient = Patient::where('user_id', $user->id)->first();

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
        $lastFollowUp = FollowUp::where('user_id', auth()->id())
            ->latest()
            ->first();

        if ($lastFollowUp && $lastFollowUp->created_at->diffInHours(now()) < 24) {
            return; // ❌ Don't send too frequently
        }

        // 📅 Check last appointment
        $lastAppointment = Appointment::where('user_id', auth()->id())
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