<?php

namespace App\Http\Controllers;

use App\Events\PusherBroadcast;
use Illuminate\Http\Request;

class PusherController extends Controller
{
    public function index()
    {
        return view('ongoing-work-request');
    }

    public function broadcast(Request $request)
    {
        broadcast(new PusherBroadcast($request->get('mesage')))->toOthers();

        return view('broadcast', ['message' => $request->get('message')]);
    }

    public function receive(Request $request)
    {
        return view('receiver', ['message' => $request->get('message')]);
    }
}
