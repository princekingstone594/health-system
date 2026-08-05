<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\DoctorAvailability;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Events\SlotBooked;
use App\Models\AiChatMemory;
use Illuminate\Support\Facades\Http;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::where('user_id', auth()->id())
            ->with('doctor')
            ->latest()
            ->get();

        return view('appointments.index', compact('appointments'));
    }

    public function create(Request $request)
    {
        $doctorId = $request->doctor_id;
        $date = $request->date;

        $slots = [];

        if ($doctorId && $date) {
            $slots = $this->getAvailableSlots($doctorId, $date);
        }

        // 🧠 PREFILL FROM AI MEMORY

        $messages = AiChatMemory::where('user_id', auth()->id())
              ->latest()
              ->take(15)
              ->get()
              ->reverse();

        $conversation = '';

        foreach ($messages as $msg) {
            $conversation = strtoupper($msg->role) . ":" . $msg->message . "\n";
        }

        $aiPrefill = [
            'reason' => null,
            'summary' => null,
            'symptoms' => null,
        ];

        if ($conversation) {
            $response = Http::withToken(env('OPENAI_AI_KEY'))
               ->post('https://api.openai.com/v1/chat/completions', [
                  'model' => 'gpt-40-mini',
                  'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Extract structured medical info this conversation. Return JSON with keys: symptoms, reason_for_visit, short_summary.'
                    ],
                    [
                        'role' => 'system',
                        'content' => $conversation
                    ]
                  ],
                  'temperature' => 0.3
               ]);
            $conent = $response['choices'][0]['message']['content'] ?? '{}';

            // Try decoding JSON
            $json = json_decode($content, true);

            if ($json) {
                $aiPrefill['symptoms'] = $json['symptoms'] ?? null;
                $aiPrefill['reason'] = $json['reason_for_visit'] ?? null;
                $aiPrefill['summary'] = $json['short_summary'] ?? null;
            }
        }

        return view('appointments.create', compact('slots', 'doctorId', 'date', 'aiPrefill'));
    }

    /**
     * 🚀 STORE (NOW WITH AI SUMMARY)
     */
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'recurrence_type' => 'nullable|in:daily,weekly,monthly',
            'recurrence_count' => 'nullable|integer|min:1|max:30'
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

            // 🔒 Check first slot
            $exists = Appointment::where('doctor_id', $doctorId)
                ->where('appointment_date', $date)
                ->where('appointment_time', $time)
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                DB::rollBack();
                return back()->with('error', 'Time slot already booked.');
            }

            // ===============================
            // 🧠 BUILD AI SUMMARY
            // ===============================
            $messages = AiChatMemory::where('user_id', auth()->id())
                ->latest()
                ->take(15)
                ->get()
                ->reverse();

            $conversation = '';

            foreach ($messages as $msg) {
                $conversation .= strtoupper($msg->role) . ": " . $msg->message . "\n";
            }

            $summary = null;

            if ($conversation) {
                $response = Http::withToken(env('OPENAI_API_KEY'))
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'You are a medical assistant. Generate a short clinical summary for a doctor including symptoms, possible condition, and urgency.'
                            ],
                            [
                                'role' => 'user',
                                'content' => $conversation
                            ]
                        ]
                    ]);

                $summary = $response['choices'][0]['message']['content'] ?? null;
            }

            // ===============================
            // ✅ CREATE FIRST APPOINTMENT
            // ===============================
            $first = Appointment::create([
                'user_id' => auth()->id(),
                'doctor_id' => $doctorId,
                'appointment_date' => $date,
                'appointment_time' => $time,
                'status' => 'pending',
                'recurrence_type' => $request->recurrence_type,
                'recurrence_count' => $request->recurrence_count,
                'ai_summary' => $summary, // ✅ ATTACHED HERE
                'reason' => $request->reason,
                'symptoms' => $request->symptoms,
            ]);

            // ===============================
            // 🔁 RECURRING LOGIC
            // ===============================
            if ($request->recurrence_type && $request->recurrence_count) {

                $baseDate = Carbon::parse($date);

                for ($i = 1; $i < $request->recurrence_count; $i++) {

                    $nextDate = match ($request->recurrence_type) {
                        'daily' => $baseDate->copy()->addDays($i),
                        'weekly' => $baseDate->copy()->addWeeks($i),
                        'monthly' => $baseDate->copy()->addMonths($i),
                    };

                    if ($nextDate->lt(now())) continue;

                    $exists = Appointment::where('doctor_id', $doctorId)
                        ->where('appointment_date', $nextDate->format('Y-m-d'))
                        ->where('appointment_time', $time)
                        ->lockForUpdate()
                        ->exists();

                    if ($exists) continue;

                    Appointment::create([
                        'user_id' => auth()->id(),
                        'doctor_id' => $doctorId,
                        'appointment_date' => $nextDate->format('Y-m-d'),
                        'appointment_time' => $time,
                        'status' => 'pending',
                        'parent_id' => $first->id,
                        'ai_summary' => $summary, // ✅ SAME SUMMARY
                    ]);
                }
            }

            DB::commit();

            // 🔥 STEP 5 — CLEAR MEMORY AFTER BOOKING
            AiChatMemory::where('user_id', auth()->id())->delete();

            event(new SlotBooked($doctorId, $date));

            return redirect()->route('appointments.index')
                ->with('success', 'Appointment booked with AI summary.');

        } catch (QueryException $e) {

            DB::rollBack();

            if ($e->getCode() == 23000) {
                return back()->with('error', 'Slot already taken.');
            }

            throw $e;
        }
    }

    public function getAvailableSlots($doctorId, $date)
    {
        $day = strtolower(Carbon::parse($date)->format('l'));

        $availability = DoctorAvailability::where('doctor_id', $doctorId)
            ->where('day_of_week', $day)
            ->first();

        if (!$availability) return [];

        $start = Carbon::parse($availability->start_time);
        $end = Carbon::parse($availability->end_time);
        $duration = $availability->slot_duration ?? 30;

        $slots = [];
        $now = Carbon::now();

        while ($start < $end) {

            if (Carbon::parse($date)->isToday() && $start->lt($now)) {
                $start->addMinutes($duration);
                continue;
            }

            $slots[] = $start->format('H:i');
            $start->addMinutes($duration);
        }

        $booked = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->pluck('appointment_time')
            ->toArray();

        return array_values(array_diff($slots, $booked));
    }

    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        $appointment->update(['status' => 'cancelled']);

        event(new SlotBooked($appointment->doctor_id, $appointment->appointment_date));

        return back()->with('success', 'Appointment cancelled.');
    }

    public function rescheduleForm($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        return view('appointments.reschedule', compact('appointment'));
    }

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

        if (Carbon::parse($request->date . ' ' . $request->time)->lt(now())) {
            return back()->with('error', 'Cannot select past time.');
        }

        try {
            DB::beginTransaction();

            $exists = Appointment::where('doctor_id', $appointment->doctor_id)
                ->where('appointment_date', $request->date)
                ->where('appointment_time', $request->time)
                ->where('id', '!=', $appointment->id)
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                DB::rollBack();
                return back()->with('error', 'Time slot already booked.');
            }

            $appointment->update([
                'appointment_date' => $request->date,
                'appointment_time' => $request->time,
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('patient.dashboard')
                ->with('success', 'Appointment rescheduled.');

        } catch (QueryException $e) {

            DB::rollBack();

            if ($e->getCode() == 23000) {
                return back()->with('error', 'Slot already taken.');
            }

            throw $e;
        }
    }

    public function slots(Request $request)
    {
        return response()->json(
            $this->getAvailableSlots($request->doctor_id, $request->date)
        );
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
                ];
            }

            $booked = Appointment::where('doctor_id', $doctorId)
                ->where('appointment_date', $date)
                ->get();

            foreach ($booked as $b) {
                $events[] = [
                    'title' => $b->parent_id ? '🔁 Recurring' : 'Booked',
                    'start' => $b->appointment_date . 'T' . $b->appointment_time,
                    'color' => 'red',
                ];
            }

            $start->addDay();
        }

        return response()->json($events);
    }
}