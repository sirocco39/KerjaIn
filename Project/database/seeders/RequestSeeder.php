<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Request;
use App\Models\User;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::nonWorker()->pluck('id');
        for( $i = 0; $i < 40; $i++) {
            Request::factory()->create([
                'requester_id' => $users->random(),
            ]);
        }
    }
}
