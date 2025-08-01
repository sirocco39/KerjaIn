<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Type\Decimal;

class ChatController extends Controller
{
    

    public function startChat(int $requestId) 
    {
        $workerId = Auth::id();
        
        $room = ChatRoom::firstOrCreate([
            'request_id' => $requestId,
            'worker_id' => $workerId,
        ], [
            'requester_id' => Request::find($requestId)->requester_id, 
        ]);


        return redirect()->route('chat.job-taker', ['selectedRoomId' => $room->id]);
    }

    public function startOffer(int $requestId, HttpRequest $request) 
    {
        $workerId = Auth::id();
        
        $room = ChatRoom::firstOrCreate([
            'request_id' => $requestId,
            'worker_id' => $workerId,
        ], [
            'requester_id' => Request::find($requestId)->requester_id, 
        ]);
        $amount = $request->input('amount'); 
        $room->offers()->create([
            'request_id'   => $room->request_id,
            'requester_id' => $room->requester_id,
            'worker_id'    => Auth::id(),
            'amount' => $amount, 
            'status' => 'open', 
        ]);

        return response()->json([
            'success'      => true,
            'redirect_url' => route('chat.job-taker', ['selectedRoomId' => $room->id])
        ]);
    }

    public function startChatWork(int $requestId) 
    {
        $workerId = Auth::id();
        
        $room = ChatRoom::firstOrCreate([
            'request_id' => $requestId,
            'worker_id' => $workerId,
        ], [
            'requester_id' => Request::find($requestId)->requester_id, 
        ]);


        return redirect()->route('chat-work.job-taker', ['selectedRoomId' => $room->id]);
    }

    public function startChatWorkReq(int $requestId) 
    {
        $workerId = Auth::id();
        
        $room = ChatRoom::firstOrCreate([
            'request_id' => $requestId,
            'worker_id' => $workerId,
        ], [
            'requester_id' => Request::find($requestId)->requester_id, 
        ]);


        return redirect()->route('chat-work.job-requester', ['selectedRoomId' => $room->id]);
    }
}
