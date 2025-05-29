<?php

namespace App\Http\Controllers;

use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;

//command for factories.
//App\Models\User::factory(10)->create();

class RequestController extends Controller
{
    function add(Request $request){

        $validated = $request->validate([
            'workTitleLabel' => 'required|string|max:255',
            'workDetailLabel' => 'required|string',
            'workPriceLabel' => 'required|numeric|min:5000',
            'workAddressLabel' => 'required|string',
            'workStartDateLabel' => 'required|date',
            'workEndDateLabel' => 'required|date',
            'workStartTimeLabel' => 'required',
            'workEndTimeLabel' => 'required',
        ]);

        $modelrequest = new ModelsRequest(); 

        //from getting data from other
        $modelrequest->requester_id = 5; 

        $startDatetime = new \DateTime("{$request->workStartDateLabel} {$request->workStartTimeLabel}:00");
        $endDatetime = new \DateTime("{$request->workEndDateLabel} {$request->workEndTimeLabel}:00");

    // Check if start is after end
        if ($startDatetime > $endDatetime) {
            return back()->withErrors([
                'workStartTimeLabel' => 'Start time must not be after end time.'
            ])->withInput();
        }

        //from handling post
        $modelrequest->title = $request->workTitleLabel;
        $modelrequest->description = $request->workDetailLabel;
        $modelrequest->price = $request->workPriceLabel;
        $modelrequest->location = $request->workAddressLabel;
        $modelrequest->work_date = $request->workDateLabel;
        $modelrequest->start_time = $startDatetime;
        $modelrequest->end_time = $endDatetime;

        //created: 
        $modelrequest->created_at = date("Y-m-d h:i:sa", time());

        $result = $modelrequest->save(); 
        if($result){
            return "request added"; 
        }else{
            return "request error"; 
        }

    }
}
