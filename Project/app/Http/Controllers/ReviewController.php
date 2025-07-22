<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\Request;


class ReviewController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'reviewer_id' => 'required|exists:users,id',
            'reviewee_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        // Directly use the validated rating
        $ratingGiven = $validated['rating'];

        // Create the review record
        Review::create([
            'transaction_id' => $validated['transaction_id'],
            'reviewer_id' => $validated['reviewer_id'],
            'reviewee_id' => $validated['reviewee_id'],
            'rating' => $ratingGiven, // Use $ratingGiven from validated data
            'comment' => $validated['comment'],
        ]);

        // Calculate the average rating for the reviewee and update the User model
        $averageRating = Review::where('reviewee_id', $validated['reviewee_id'])->avg('rating');
        User::where('id', $validated['reviewee_id'])->update(['rating' => $averageRating]);

        // Changed from JSON response to redirect with custom alert
        return back()->with('custom_success_alert', 'Ulasan Anda berhasil disimpan!');
    }
}
