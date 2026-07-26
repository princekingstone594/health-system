<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\DoctorAvailability;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class AppointmentController extends Controller
{
    /**
     * List user appointments
     */
    public function index()
    {
        $appointments = Appointment::where('user_id', auth()->id())
            ->with('doctor')
            ->latest()
            ->get();

        return view('appointments.index', compact('appointments'));
    }

    /**
     * Show booking page
     */
    public function create(Request $request)
    {
        $doctorId = $request->doctor_id;
        $date = $request->date;

        $slots = [];

        if ($doctorId && $date) {
            $slots = $this->getAvailableSlots($doctorId, $date);
        }

        return view('appointments.create', compact('slots', 'doctorId', 'date'));
    }

    /**
     * Store appointment (RACE CONDITION SAFE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
        ]);

        $doctorId = $request->doctor_id;
        $date = $request->date;
        $time = $request->time;

        // 🚫 Prevent past booking
        if (Carbon::parse($date . ' ' . $time)->lt(now())) {
            return back()->with('error', 'Cannot book past time.');
        }

        try {
            DB::beginTransaction();

            // 🔒 Lock rows for this doctor & date
            $exists = Appointment::where('doctor_id', $doctorId)
                ->where('date', $date)
                ->where('time', $time)
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                DB::rollBack();
                return back()->with('error', 'Time slot already booked.');
            }

            Appointment::create([
                'user_id' => auth()->id(),
                'doctor_id' => $doctorId,
                'date' => $date,
                'time' => $time,
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('appointments.index')
                ->with('success', 'Appointment booked. Waiting for doctor approval.');

        } catch (QueryException $e) {

            DB::rollBack();

            // 🔥 Handle DB unique constraint
            if ($e->getCode() == 23000) {
                return back()->with('error', 'Slot already taken. Please choose another.');
            }

            throw $e;
        }
    }

    /**
     * Generate available slots
     */
    public function getAvailableSlots($doctorId, $date)
    {
        $day = strtolower(Carbon::parse($date)->format('l'));

        $availability = DoctorAvailability::where('doctor_id', $doctorId)
            ->where('day_of_week', $day)
            ->first();

        if (!$availability) {
            return [];
        }

        $start = Carbon::parse($availability->start_time);
        $end = Carbon::parse($availability->end_time);
        $duration = $availability->slot_duration ?? 30;

        $slots = [];
        $now = Carbon::now();

        while ($start < $end) {

            // 🚫 Skip past times (only today)
            if (Carbon::parse($date)->isToday() && $start->lt($now)) {
                $start->addMinutes($duration);
                continue;
            }

            $slots[] = $start->format('H:i');
            $start->addMinutes($duration);
        }

        // 🚫 Remove booked slots
        $booked = Appointment::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->pluck('time')
            ->toArray();

        return array_values(array_diff($slots, $booked));
    }

    /**
     * Cancel appointment
     */
    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        $appointment->update([
            'status' => 'cancelled'
        ]);

        return back()->with('success', 'Appointment cancelled.');
    }

    /**
     * Show reschedule form
     */
    public function rescheduleForm($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        return view('appointments.reschedule', compact('appointment'));
    }

    /**
     * Save reschedule (RACE CONDITION SAFE)
     */
    public function reschedule(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
        ]);

        // 🚫 Prevent past booking
        if (Carbon::parse($request->date . ' ' . $request->time)->lt(now())) {
            return back()->with('error', 'Cannot select past time.');
        }

        try {
            DB::beginTransaction();

            $exists = Appointment::where('doctor_id', $appointment->doctor_id)
                ->where('date', $request->date)
                ->where('time', $request->time)
                ->where('id', '!=', $appointment->id)
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                DB::rollBack();
                return back()->with('error', 'Time slot already booked.');
            }

            $appointment->update([
                'date' => $request->date,
                'time' => $request->time,
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('patient.dashboard')
                ->with('success', 'Appointment rescheduled. Awaiting approval.');

        } catch (QueryException $e) {

            DB::rollBack();

            if ($e->getCode() == 23000) {
                return back()->with('error', 'Slot already taken.');
            }

            throw $e;
        }
    }

    /**
     * AJAX: Load slots
     */
    public function slots(Request $request)
    {
        $doctorId = $request->doctor_id;
        $date = $request->date;

        if (!$doctorId || !$date) {
            return response()->json([]);
        }

        $slots = $this->getAvailableSlots($doctorId, $date);

        return response()->json($slots);
    }

    public function calendarEvents(Request $request)
    {
        $doctorId = $request->doctor_id;
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        $events = [];

        while ($start <= $end) {
            $date = $start->format('Y-m-d');
            $slots = $this->getAvailableSlots($doctorId, $date);

            foreach ($slots as $time) {
                
                $events[] = [
                    'title' => 'Available',
                    'start' => $date . 'T' . $time,
                    'color' => 'green',
                    'booked' => false,
                ];
            }

            // 🔴 Add booked slots
            $booked = Appointment::where('doctor_id', $doctorId)
                 ->where('date', $date)
                 ->get();

            foreach ($booked as $b) {
                $events[] = [
                    'title' => 'Booked',
                    'start' => $b->date . 'T' . $b->time,
                    'color' => 'red',
                    'booked' => true,
                ];
            }

            $start->addday();
        }

        return response()->json($events);
    }
}