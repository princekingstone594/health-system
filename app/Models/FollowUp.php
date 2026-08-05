<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FollowUp extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'appointment_id',
        'message',
    ];

    /**
     * 🧑‍⚕️ Doctor who generated the follow-up
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * 🧑 Patient receiving the follow-up
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * 📅 Related appointment
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}