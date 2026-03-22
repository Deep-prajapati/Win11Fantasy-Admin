<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserConversationMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['conversation_id', 'sender_id', 'message', 'status', 'is_deleted', 'is_deleted_both'];

    public function conversation()
    {
        return $this->belongsTo(UserConversation::class, 'conversation_id', 'conversation_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
