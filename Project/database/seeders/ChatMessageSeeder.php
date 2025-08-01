<?php

namespace Database\Seeders;

use App\Models\ChatRoom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ChatMessage;

class ChatMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chatrooms = ChatRoom::all();
        foreach ($chatrooms as $chatroom) {
            ChatMessage::factory()->count(10)->create([
                'chat_room_id' => $chatroom->id,
                'sender_id' => $chatroom->worker_id, 
                'receiver_id' => $chatroom->requester_id, 
            ]);
        }
    }
}
