<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatMemory extends Model
{
    protected $fillable = [
        'patient_id',
        'key',
        'value',
        'type'
    ];
}