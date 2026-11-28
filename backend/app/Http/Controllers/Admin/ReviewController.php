<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\UpdateReviewStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListReviewsRequest;
use App\Http\Requests\Admin\UpdateReviewStatusRequest;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    private const LIST_FIELDS = ['id', 'booking_id', 'title', 'rating', 'status', 'is_anonymous', 'created_at'];

    private const MODERATION_FIELDS = ['moderation_reason', 'moderated_by_user_id', 'moderated_at'];

    public function index(ListReviewsRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $query = Review::query()
            ->with('booking:id,reference')
            ->where(array_intersect_key($filters, ['status' => true, 'rating' => true]));

        if (isset($filters['search'])) {
            $pattern = '%'.strtr($filters['search'], ['!' => '!!', '%' => '!%', '_' => '!_']).'%';

            $query->where(function (Builder $review) use ($pattern): void {
                $review->whereRaw("reviews.title LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereHas('booking', fn (Builder $booking) => $booking->whereRaw("bookings.reference LIKE ? ESCAPE '!'", [$pattern]));
            });
        }

        $reviews = $query->orderByDesc('created_at')->orderByDesc('id')
            ->paginate(15, self::LIST_FIELDS)
            ->appends($filters);

        return response()->json($reviews);
    }

    public function show(string $review): JsonResponse
    {
        $record = Review::query()
            ->with('booking:id,reference')
            ->findOrFail($review, [
                ...self::LIST_FIELDS,
                'comment', 'quality_rating', 'communication_rating', 'value_rating',
                'provider_reply', 'provider_replied_at', 'review_media',
                ...self::MODERATION_FIELDS, 'updated_at',
            ]);

        return response()->json(['data' => $record]);
    }

    public function updateStatus(
        UpdateReviewStatusRequest $request,
        UpdateReviewStatus $update,
        string $review,
    ): JsonResponse {
        $change = $request->validated();
        $record = $update->execute(
            $review,
            $request->user()->getAuthIdentifier(),
            $change['status'],
            $change['reason'],
        );

        return response()->json([
            'data' => $record->only(['id', 'status', ...self::MODERATION_FIELDS]),
        ]);
    }
}
