<?php

namespace App\Models;

use App\Models\UserConversationMessage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserConversation extends Model
{
    use HasFactory;

    protected $fillable = ['conversation_id', 'sender_id', 'receiver_id'];

    public function messages()
    {
        return $this->hasMany(UserConversationMessage::class, 'conversation_id', 'conversation_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    public function lastMessage()
    {
        return $this->hasOne(UserConversationMessage::class, 'conversation_id', 'conversation_id')
            ->latest('created_at');
    }
}
