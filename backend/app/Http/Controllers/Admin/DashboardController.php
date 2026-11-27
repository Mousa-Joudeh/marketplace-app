<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use App\Models\ProviderProfile;
use App\Models\Service;
use App\Models\Space;
use Illuminate\Http\JsonResponse;

class DashboardController
{
    public function __invoke(): JsonResponse
    {
        $recentBookings = Booking::query()
            ->latest()
            ->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'reference', 'status', 'created_at']);

        return response()->json([
            'metrics' => [
                'bookings' => Booking::query()->count(),
                'spaces' => Space::query()->count(),
                'services' => Service::query()->count(),
                'pending_providers' => ProviderProfile::query()->where('approval_status', 'pending')->count(),
            ],
            'recent_bookings' => $recentBookings,
        ])->header('Cache-Control', 'no-store');
    }
}
