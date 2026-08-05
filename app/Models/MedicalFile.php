<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalFile extends Model
{
    protected $fillable = [
        'appointment_id',
        'user_id',
        'file_path',
        'original_name'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}