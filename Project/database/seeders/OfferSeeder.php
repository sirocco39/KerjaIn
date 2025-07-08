<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Request;
use App\Models\ChatRoom;
use App\Models\Offer;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $openChatRooms = ChatRoom::where('is_open', true)->get();
        $closedChatRooms = ChatRoom::where('is_open', false)->get();
        foreach($openChatRooms as $chatRoom) {
            Offer::factory()->openOffer()->create([
                'chat_room_id' => $chatRoom->id,
                'request_id' => $chatRoom->request_id,
                'requester_id' => $chatRoom->requester_id,
                'worker_id' => $chatRoom->worker_id,
            ]);
        }
        foreach($closedChatRooms as $chatRoom) {
            Offer::factory()->acceptedOffer()->create([
                'chat_room_id' => $chatRoom->id,
                'request_id' => $chatRoom->request_id,
                'requester_id' => $chatRoom->requester_id,
                'worker_id' => $chatRoom->worker_id,
            ]);
        }

    }
}
