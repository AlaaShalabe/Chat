<?php

namespace App\Http\Controllers\Chat;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\MessageRequest;
use App\Models\Chat;
use App\Models\Message;
use App\Notifications\ChatMessageeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{

    public function sendMessage(MessageRequest $request)
    {
        $request->validated();
        $to =  $request->to_user;
        $from = Auth::user()->id;
        $chat = Message::where(function ($query) use ($request) {
            $query->where('from_user', Auth::user()->id)->where('to_user', $request->to_user);
        })->orWhere(function ($query) use ($request) {
            $query->where('from_user', $request->to_user)->where('to_user', Auth::user()->id);
        })->select('chat_id')->first();

        if ($chat) {
            $chat_id = $chat->chat_id;
        } else {
            $Chat = new Chat();
            $chat->save();
            $chat_id = $Chat->id;
            Message::create([
                'message' => $request->message,
                'to_user' => $to,
                'from_user' => $from,
                'chat_id' => $chat_id
            ]);
        }
        $to->notify(new ChatMessageeNotification($request->message));
        broadcast(new ChatMessageSent($request->message));
        return response()->json([
            'message' =>  $request->message,
            'success' => true
        ], 200);
    }
}
