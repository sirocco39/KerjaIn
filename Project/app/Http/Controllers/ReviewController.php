<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate the incoming request data
            $validated = $request->validate([
                'transaction_id' => 'required|exists:transactions,id',
                'reviewer_id' => 'required|exists:users,id',
                'reviewee_id' => 'required|exists:users,id',
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required|string',
            ]);

            // Authorization check: Ensure the reviewer is the authenticated user
            if (Auth::id() != $validated['reviewer_id']) {
                return response()->json([
                    'success' => false,
                    'message' => __('alerts.not_authorized_to_review')
                ], 403);
            }

            // Prevent duplicate reviews from the same reviewer for the same transaction
            $existingReview = Review::where('transaction_id', $validated['transaction_id'])
                ->where('reviewer_id', $validated['reviewer_id'])
                ->first();

            if ($existingReview) {
                return response()->json([
                    'success' => false,
                    'message' => __('alerts.review_already_given')
                ], 409);
            }

            // Create the review record
            Review::create([
                'transaction_id' => $validated['transaction_id'],
                'reviewer_id' => $validated['reviewer_id'],
                'reviewee_id' => $validated['reviewee_id'],
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]);

            // Calculate the average rating for the reviewee and update the User model
            $averageRating = Review::where('reviewee_id', $validated['reviewee_id'])->avg('rating');
            User::where('id', $validated['reviewee_id'])->update(['rating' => $averageRating]);

            // Return a JSON success response for AJAX requests
            return response()->json([
                'success' => true,
                'message' => __('alerts.review_saved_success')
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => __('alerts.validation_failed') . ': ' . $e->getMessage(), // Tambahkan pesan validasi di akhir
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('alerts.terjadi_kesalahan') . ' saat menyimpan ulasan: ' . $e->getMessage() // Tambahkan pesan error di akhir
            ], 500);
        }
    }
}
