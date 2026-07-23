<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    public function doctors()
    {
        return $this->belongsToMany(User::class)->where('role', 'doctor');
    }
}
