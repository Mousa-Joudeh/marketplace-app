<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListWorkQueueRequest;
use App\Models\Booking;
use App\Models\ProviderProfile;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;

class WorkQueueController extends Controller
{
    public function __invoke(ListWorkQueueRequest $request): JsonResponse
    {
        $type = $request->validated('type', ListWorkQueueRequest::PENDING_PROVIDERS);
        $now = CarbonImmutable::now('UTC');

        $providers = ProviderProfile::query()->where('approval_status', 'pending');
        $bookings = Booking::query()->where('status', 'pending')
            ->whereBetween('starts_at', [$now, $now->addHours(24)]);

        $counts = [
            ListWorkQueueRequest::PENDING_PROVIDERS => $providers->count(),
            ListWorkQueueRequest::UPCOMING_PENDING_BOOKINGS => $bookings->count(),
        ];

        $items = match ($type) {
            ListWorkQueueRequest::PENDING_PROVIDERS => $providers
                ->orderByRaw('created_at IS NULL')
                ->orderBy('created_at')->orderBy('id')
                ->paginate(15, ['id', 'display_name', 'city', 'approval_status', 'created_at'], total: $counts[$type]),
            ListWorkQueueRequest::UPCOMING_PENDING_BOOKINGS => $bookings
                ->orderBy('starts_at')->orderBy('id')
                ->paginate(15, ['id', 'reference', 'status', 'starts_at', 'provider_profile_id'], total: $counts[$type]),
        };

        return response()->json([
            'type' => $type,
            'counts' => $counts,
            'items' => $items->appends(['type' => $type]),
        ])->header('Cache-Control', 'no-store');
    }
}
