<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Type\Decimal;

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

    public function startOffer(int $requestId, HttpRequest $request) // nama beda dengan Illuminate\Http\Request
    {
        $workerId = Auth::id();
        // Cek apakah chatroom sudah ada
        $room = ChatRoom::firstOrCreate([
            'request_id' => $requestId,
            'worker_id' => $workerId,
        ], [
            'requester_id' => Request::find($requestId)->requester_id, // Ambil requester_id dari model Request
        ]);
        $amount = $request->input('amount'); // Ambil nilai tawaran dari input, default 0 jika tidak ada
        $room->offers()->create([
            'request_id'   => $room->request_id,
            'requester_id' => $room->requester_id,
            'worker_id'    => Auth::id(),
            'amount' => $amount, // Atau nilai default lainnya
            'status' => 'open', // Status awal tawaran
        ]);

        return response()->json([
            'success'      => true,
            'message'      => 'Tawaran berhasil dikirim!',
            'redirect_url' => route('chat.job-taker', ['selectedRoomId' => $room->id])
        ]);
    }
}
