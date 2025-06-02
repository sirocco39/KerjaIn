<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChatRoom;
use App\Models\Request;
use App\Models\User;

class ChatRoomSeeder extends Seeder
{
    public function run(): void
    {
        $openRequests = Request::where('status', 'open')->get();
        $closedRequests = Request::where('status', 'closed')->get();
        $workerIds = User::where('is_worker', true)->pluck('id');

        foreach ($openRequests as $request) {
            ChatRoom::factory()->create([
                'request_id'    => $request->id,
                'requester_id'  => $request->requester_id,
                'worker_id'     => $workerIds->random(),
                'is_open'      => true,
            ]);
        }
        foreach ($closedRequests as $request) {
            ChatRoom::factory()->create([
                'request_id'    => $request->id,
                'requester_id'  => $request->requester_id,
                'worker_id'     => $workerIds->random(),
                'is_open'      => false,
            ]);
        }
    }
}
