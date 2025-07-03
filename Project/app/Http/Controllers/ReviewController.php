<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        Review::create([
            'transaction_id' => $transaction->id,
            'reviewer_id' => Auth::id(),
            'reviewee_id' => $transaction->worker_id,
            'rating' => $validated['rating'] ?? 5,
            'comment' => $validated['comment'],
        ]);


        return back()->with('success', 'Review berhasil dikirim.');
    }
}
