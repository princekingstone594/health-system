<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
use App\Models\Doctor;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'appointment_time',
        'status',
        'is_paid',
        'doctor_notes',
        'diagnosis',
        'prescription',
        'is_shared_with_patient',
        'recurrence_type',
        'recurrence_count',
        'parent_id',
        'ai_summary',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function medicalFiles()
    {
        return $this->hasMany(MedicalFile::class);
    }
}