<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\Request as RequestModel; // Avoid conflict with the Request facade

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        #get 5 latest open requests and deleted_at is null

        $fiveLatestRequests = RequestModel::whereNull('deleted_at')
            ->where('status', 'open')
            ->latest()
            ->take(5)
            ->get();
        // $fiveLatestRequests = Request::latest()->where() take(5)->get();
        return view('Job_Requester.beranda', compact('fiveLatestRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Show the form for creating a new request
        return view('Job_Requester.postwork');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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

        $workRequest = new RequestModel();

        //from getting data from other
        $workRequest->requester_id = auth()->id; // Assuming the user is authenticated

        $startDatetime = new \DateTime("{$request->workStartDateLabel} {$request->workStartTimeLabel}:00");
        $endDatetime = new \DateTime("{$request->workEndDateLabel} {$request->workEndTimeLabel}:00");

        // Check if start is after end
        if ($startDatetime > $endDatetime) {
            return back()->withErrors([
                'datetime' => 'Start time must not be after end time.'
            ])->withInput();
        }

        //from handling post
        $workRequest->title = $request->workTitleLabel;
        $workRequest->slug = Str::slug($workRequest->title);
        $workRequest->description = $request->workDetailLabel;
        $workRequest->price = $request->workPriceLabel;
        $workRequest->location = $request->workAddressLabel;
        $workRequest->start_time = $startDatetime;
        $workRequest->end_time = $endDatetime;

        //created: 
        $workRequest->created_at = date("Y-m-d h:i:sa", time());

        $result = $workRequest->save();
        if ($result) {
            return redirect()->route('requesttt.show', $workRequest->slug);
        } else {
            return "request error";
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        //get the request by slug
        $workRequest = RequestModel::where('slug', $slug)->firstOrFail();
        // If the request is not found, it will throw a 404 error
        // Return the view with the request data
        if (!$workRequest || $workRequest->deleted_at) {
            abort(404, 'Request not found or has been deleted.');
        }
        // Check if the request is open
        if ($workRequest->status !== 'open') {
            abort(404, 'Request is not open.');
        }

        return view('Job_Requester.request', compact('workRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug)
    {

        // Find the request by slug
        $workRequest = RequestModel::where('slug', $slug)->firstOrFail();
        // If the request is not found, it will throw a 404 error
        if (!$workRequest || $workRequest->deleted_at) {
            abort(404, 'Request not found or has been deleted.');
        }

        // Return the edit view with the request data
        return view('Job_Requester.edit', compact('workRequest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        //
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
        $workRequest = RequestModel::where('slug', $slug)->firstOrFail();
        $workRequest->title = $request->workTitleLabel;
        $workRequest->slug = Str::slug($workRequest->title);
        $workRequest->description = $request->workDetailLabel;
        $workRequest->price = $request->workPriceLabel;
        $workRequest->location = $request->workAddressLabel;
        $startDatetime = new \DateTime("{$request->workStartDateLabel} {$request->workStartTimeLabel}:00");
        $endDatetime = new \DateTime("{$request->workEndDateLabel} {$request->workEndTimeLabel}:00");
        // Check if start is after end
        if ($startDatetime > $endDatetime) {
            return back()->withErrors([
                'datetime' => 'Start time must not be after end time.'
            ])->withInput();
        }
        $workRequest->start_time = $startDatetime;
        $workRequest->end_time = $endDatetime;
        //updated:
        $workRequest->updated_at = date("Y-m-d h:i:sa", time());

        // now update the request on database
        $result = $workRequest->save();
        if ($result) {
            return redirect()->route('requesttt.show', $workRequest->slug);
        } else {
            return "request update error";
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $workRequest = RequestModel::where('slug', $slug)->firstOrFail();
        $workRequest->deleted_at = date("Y-m-d h:i:sa", time());
        $workRequest->status = 'closed'; // Optionally set status to deleted
        $result = $workRequest->save();
        if ($result) {
            return redirect()->to('/job-req/beranda');
        } else {
            return back()->withErrors(['error' => 'Failed to delete request.']);
        }
    }
}
