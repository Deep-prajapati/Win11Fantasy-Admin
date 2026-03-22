<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Helpers\Helper;
use App\Events\TestEvent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\ChatMessageSent;
use App\Events\SendMessageEvent;
use Illuminate\Validation\Rule;
use App\Models\UserConversation;
use App\Http\Controllers\Controller;
use App\Models\UserConversationMessage;
use Illuminate\Support\Facades\Validator;

class UserChatController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $chats = UserConversation::where('sender_id', $user->id)->orwhere('receiver_id', $user->id)
        ->with('lastMessage:conversation_id,message,sender_id,created_at as time')
        ->with('sender', 'receiver')->get();
        return  Helper::SuccessReturn($chats, 'Success');
    }
    public function chatusers(Request $request)
    {
        $user = auth()->user();
        $users = User::query();
        $users = $users->whereNot('id', $user->id);
        $users = $users->get();
        return Helper::SuccessReturn($users, 'Data list fatched');
    }
    public function conversation(Request $request)
    {
        $rules = [
            'sender_id' => ['required', Rule::exists('users', 'id')],
            'receiver_id' => ['required', Rule::exists('users', 'id')],
        ];
        $validator = Validator::make($request->all(), $rules,);
        if ($validator->fails()) {
            return Helper::FalseReturn(null, $validator->errors()->first());
        }
        $conversation = UserConversation::where(function ($query) use ($request) {
            $query->where('sender_id', $request->sender_id)
                ->where('receiver_id', $request->receiver_id);
        })
            ->orWhere(function ($query) use ($request) {
                $query->where('sender_id', $request->receiver_id)
                    ->where('receiver_id', $request->sender_id);
            })
            ->with('lastMessage:conversation_id,message,sender_id,created_at as time')
            ->first();
        if (!$conversation) {
            $conversation = UserConversation::create([
                'conversation_id' => Str::uuid(),
                'sender_id' => $request->sender_id,
                'receiver_id' => $request->receiver_id
            ]);
        }

        return Helper::SuccessReturn($conversation, 'Data Found.');
    }
    public function sendMessage(Request $request, $conversation)
    {
        $conversation = UserConversation::where('conversation_id', $conversation)->first();

        if (!$conversation) {
            return Helper::FalseReturn(null, 'Conversation not found');
        }

        $receiverId = $conversation->sender_id == $request->sender_id
            ? $conversation->receiver_id
            : $conversation->sender_id;

        $message = $conversation->messages()->create([
            'sender_id' => $request->sender_id,
            'message' => $request->message,
        ]);
        // UserConversationMessage::create([
        //     'conversation_id' => $conversation,
        //     'sender_id' => $request->sender_id,
        //     'message' => $request->message,
        // ]);
        // Broadcast the message
        event(new SendMessageEvent($conversation->conversation_id, $request->message, $request->sender_id, $receiverId));
        return Helper::SuccessReturn(null, 'success');
    }
    public function messages(Request $request, $conversation)
    {
        $conversation = UserConversation::where('conversation_id', $conversation)->first();

        if (!$conversation) {
            return Helper::FalseReturn(null, 'Conversation not found');
        }
        $message = UserConversationMessage::where('conversation_id', $conversation->conversation_id)->select('message', 'sender_id', 'created_at as time')->orderby('id', 'asc')->get();
        return Helper::SuccessReturn($message, 'All data');
    }
    // public function store(Request $request)
    // {
    //     $senderId = 1;
    //     $message = $request->input('message', 'Hello i am testing');
    //     $receiverId = $request->input('receiverId', 1);

    //     event(new ChatMessageSent($message, $senderId, $receiverId));
    //     return response()->json(['success' => true, 'message' => 'Message sent']);
    // }
}
