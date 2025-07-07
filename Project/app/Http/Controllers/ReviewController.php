<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Transaction;
use App\Models\User;
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

        $ratingGiven = $validated['rating'] ?? 5;

        Review::create([
            'transaction_id' => $transaction->id,
            'reviewer_id' => Auth::id(),
            'reviewee_id' => $transaction->worker_id,
            'rating' => $ratingGiven,
            'comment' => $validated['comment'],
        ]);

        // Update status transaction menjadi selesai
        $transaction->status = 'Completed';
        $transaction->save();

        // Kalkulasi dan update rating di tabel users
        $worker = User::findOrFail($transaction->worker_id);

        // Hitung total review sebelumnya (sebelum review ini masuk)
        $totalReviewsBefore = Review::where('reviewee_id', $worker->id)->count() - 1;

        if ($totalReviewsBefore < 0) {
            $totalReviewsBefore = 0;
        }

        $totalRatingBefore = $worker->rating * $totalReviewsBefore;

        $totalRatingNow = $totalRatingBefore + $ratingGiven;
        $totalReviewsNow = $totalReviewsBefore + 1;

        $newAverage = $totalRatingNow / $totalReviewsNow;

        // Update kolom rating pada tabel users
        $worker->rating = $newAverage;
        $worker->save();

        return back()->with('success', 'Review berhasil dikirim.');
    }
}
