<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatMemory extends Model
{
    protected $fillable = [
        'user_id',
        'key',
        'value',
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(user::class);
    }
}