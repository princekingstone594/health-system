<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorAvailability extends Model
{
    protected $fillable = [
        'doctor_id',
        'day',
        'start_time',
        'end_time',
        'slot_duration',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
