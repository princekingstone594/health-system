<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Appointment;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);

        // 🔒 SECURITY CHECKS
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($appointment->status !== 'approved') {
            return back()->with('error', 'You can only review completed appointments.');
        }

        if (now()->lt($appointment->date)) {
            return back()->with('error', 'You can only review after appointment date.');
        }

        // Prevent duplicate review
        $exists = Review::where('appointment_id', $appointment->id)->exists();

        if ($exists) {
            return back()->with('error', 'You already reviewed this appointment.');
        }

        Review::create([
            'user_id' => auth()->id(),
            'doctor_id' => $appointment->doctor_id,
            'appointment_id' => $appointment->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Review submitted successfully!');
    }
}