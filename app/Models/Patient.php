<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];

    public function appointments()
    {
        return $this->hasMany(App\Models\Appointment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}