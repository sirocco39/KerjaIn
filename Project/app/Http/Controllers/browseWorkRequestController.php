<?php

namespace App\Http\Controllers;

use App\Models\Request as WorkRequest; 
use Illuminate\Http\Request;
use Carbon\Carbon; 
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;

class BrowseWorkRequestController extends Controller
{
    /**
     * Display a listing of open work requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        
        $query = WorkRequest::where('status', 'open')
                            ->where('requester_id', '!=', Auth::id()) 
                            ->where('start_time', '>', now()) 
                            ->with('requester')
                            ->orderBy('created_at', 'desc'); 

        
        $query = $this->applySearchFilters($query, $request);

        
        $workRequests = $query->paginate(10); 

        
        return view('job-taker.browse-work', compact('workRequests'));
    }

    /**
     * Apply search filters to the given query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applySearchFilters(Builder $query, Request $request): Builder
    {
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%');
                
                
                
            });
        }

        return $query;
    }

    /**
     * Display the specified work request.
     * This method could be used for a detailed view of a single request,
     * though your current design suggests showing details in the right panel.
     * For now, we'll keep it simple for the browse page.
     *
     * @param  \App\Models\Request  $request 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(WorkRequest $request)
    {
        
        $request->load('requester');

        return response()->json([
            'id' => $request->id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'location' => $request->location,
            'start_time' => $request->start_time->format('Y-m-d H:i'), 
            'end_time' => $request->end_time->format('Y-m-d H:i'),      
            'display_date' => $request->start_time->format('d M Y, H:i'), 
            'display_time_range' => $request->end_time->format('d M Y, H:i'),
            'requester_first_name' => $request->requester->first_name
            
        ]);
    }
}