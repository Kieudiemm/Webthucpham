<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';

    protected $fillable = [
        'user_id',
        'guest_token',
        'sender',
        'message',
    ];

    /**
     * Liên kết tới User (1 user có nhiều tin nhắn)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
