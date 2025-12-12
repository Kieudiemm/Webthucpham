<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Jenssegers\Mongodb\Eloquent\Model;

class ChatMessage extends Model
{
    use SoftDeletes;

    protected $connection = 'mongodb';
    protected $collection = 'chat_messages';

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
