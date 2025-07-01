<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    //use App\Models\ChatRoom;

    public function startChat(int $requestId) // nama beda dengan Illuminate\Http\Request
    {
        $workerId = Auth::id();
        // Cek apakah chatroom sudah ada
        $room = ChatRoom::firstOrCreate([
            'request_id' => $requestId,
            'worker_id' => $workerId,
        ], [
            'requester_id' => Request::find($requestId)->requester_id, // Ambil requester_id dari model Request
        ]);


        return redirect()->route('chat.job-taker', ['selectedRoomId' => $room->id]);
}

}
