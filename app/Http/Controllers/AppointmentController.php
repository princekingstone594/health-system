public function reschedule(Request $request, $id)
{
    $appointment = Appointment::findOrFail($id);
    $newDate = $request->appointment_date;

    // 🚫 LEAVE
    $onLeave = Leave::where('doctor_id', $appointment->doctor_id)
        ->whereDate('start_date', '<=', $newDate)
        ->whereDate('end_date', '>=', $newDate)
        ->exists();

    if ($onLeave) {
        return response()->json(['error' => 'Doctor is on leave']);
    }

    // ❗ DOUBLE BOOK
    $exists = Appointment::where('doctor_id', $appointment->doctor_id)
        ->where('appointment_date', $newDate)
        ->where('appointment_time', $appointment->appointment_time)
        ->where('id', '!=', $appointment->id)
        ->where('status', '!=', 'Cancelled')
        ->exists();

    if ($exists) {
        return response()->json(['error' => 'Time slot already booked']);
    }

    $appointment->update(['appointment_date' => $newDate]);

    return response()->json(['success' => true]);
}