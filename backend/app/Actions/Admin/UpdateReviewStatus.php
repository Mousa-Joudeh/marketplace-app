<?php

namespace App\Actions\Admin;

use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateReviewStatus
{
    public function execute(string $reviewId, int $adminId, string $status, string $reason): Review
    {
        [$review, $previousStatus] = DB::transaction(function () use ($reviewId, $adminId, $status, $reason): array {
            $review = Review::query()->lockForUpdate()->findOrFail($reviewId);
            $previousStatus = $review->status;

            abort_if($previousStatus === $status, 409, 'The review already has this status.');

            $review->status = $status;
            $review->moderation_reason = $reason;
            $review->moderated_by_user_id = $adminId;
            $review->moderated_at = now();
            $review->save();

            return [$review, $previousStatus];
        }, 3);

        try {
            Log::channel('single')->info('Review status changed', [
                'admin_id' => $adminId,
                'review_id' => $review->getKey(),
                'previous_status' => $previousStatus,
                'new_status' => $status,
                'reason' => $reason,
            ]);
        } catch (Throwable) {
            error_log("Could not log review status change for review {$review->getKey()} by admin {$adminId}.");
        }

        return $review;
    }
}
