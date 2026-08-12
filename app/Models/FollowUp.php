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
        'user_id',
        'appointment_id',
        'message',
        'response',
        'status',
        'ai_status',
    ];

    /**
     * 🧑 Patient receiving the follow-up
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 📅 Related appointment
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}