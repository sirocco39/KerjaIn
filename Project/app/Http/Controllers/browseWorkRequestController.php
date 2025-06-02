<?php

namespace App\Http\Controllers;

use App\Models\Request as WorkRequest; // Alias Request to WorkRequest to avoid conflict with Illuminate\Http\Request
use Illuminate\Http\Request;
use Carbon\Carbon; // For date and time formatting

class browseWorkRequestController extends Controller
{
    /**
     * Display a listing of open work requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Start with all open requests
        $query = WorkRequest::where('status', 'open')
                           ->where('end_time', '>', now()) // Only show requests that haven't passed their end time
                           ->orderBy('created_at', 'desc'); // Order by newest first

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%');
                //   ->orWhere('description', 'like', '%' . $searchTerm . '%')
                //   ->orWhere('location', 'like', '%' . $searchTerm . '%');
            });
        }

        // Paginate the results (optional, but good for many requests)
        $workRequests = $query->paginate(10); // Adjust items per page as needed

        // Pass the work requests to the view
        return view('workRequest.browseWorkRequest', compact('workRequests'));
    }

    /**
     * Display the specified work request.
     * This method could be used for a detailed view of a single request,
     * though your current design suggests showing details in the right panel.
     * For now, we'll keep it simple for the browse page.
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */

    public function show(WorkRequest $request) // Menggunakan route model binding
    {
        // Pastikan relasi requester dimuat jika Anda ingin menampilkan info requester
        // $request->load('requester');

        return response()->json([
            'id' => $request->id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'location' => $request->location,
            'start_time' => $request->start_time->format('Y-m-d H:i'), // Format untuk JS
            'end_time' => $request->end_time->format('Y-m-d H:i'),     // Format untuk JS
            'display_date' => $request->start_time->format('d M Y'), // Untuk tampilan '19 Mei 2025'
            'display_time_range' => $request->start_time->format('H.i') . ' - ' . $request->end_time->format('H.i'),
            // Tambahkan data lain yang mungkin Anda perlukan di detail panel
        ]);
    }
}