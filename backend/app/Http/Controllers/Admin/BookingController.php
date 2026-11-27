<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListBookingsRequest;
use App\Models\Booking;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    private const LIST_FIELDS = [
        'id', 'reference', 'status', 'customer_id', 'provider_profile_id',
        'starts_at', 'ends_at', 'total_amount', 'currency', 'created_at',
    ];

    private const PARTICIPANTS = ['customer:id,name', 'providerProfile:id,display_name'];

    public function index(ListBookingsRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $query = Booking::query()->with(self::PARTICIPANTS);

        if (isset($filters['search'])) {
            $pattern = '%'.strtr($filters['search'], ['!' => '!!', '%' => '!%', '_' => '!_']).'%';
            $query->whereRaw("reference LIKE ? ESCAPE '!'", [$pattern]);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['provider_id'])) {
            $query->where('provider_profile_id', $filters['provider_id']);
        }

        if (isset($filters['date_from'])) {
            $start = CarbonImmutable::parse($filters['date_from'], 'Asia/Amman')->utc();
            $query->where('starts_at', '>=', $start);
        }

        if (isset($filters['date_to'])) {
            $end = CarbonImmutable::parse($filters['date_to'], 'Asia/Amman')->addDay()->utc();
            $query->where('starts_at', '<', $end);
        }

        $bookings = $query->orderByDesc('created_at')->orderByDesc('id')
            ->paginate(15, self::LIST_FIELDS)
            ->appends($filters);

        return response()->json($bookings);
    }

    public function show(string $booking): JsonResponse
    {
        $record = Booking::query()->with([
            ...self::PARTICIPANTS,
            'items' => fn (HasMany $items) => $items->select([
                'id', 'booking_id', 'space_id', 'service_id', 'package_id', 'equipment_id', 'parent_booking_item_id',
                'item_role', 'title_snapshot', 'quantity', 'unit_price', 'pricing_unit_snapshot',
                'billable_minutes', 'line_total_amount', 'starts_at', 'ends_at',
            ])->orderBy('id'),
            'payments' => fn (HasMany $payments) => $payments->select([
                'id', 'booking_id', 'reference', 'method', 'status', 'transaction_type',
                'parent_payment_id', 'amount', 'currency', 'paid_at', 'processed_at', 'created_at',
            ])->orderBy('id'),
        ])->findOrFail($booking, [
            ...self::LIST_FIELDS,
            'agreed_scope', 'address', 'participant_count', 'subtotal_amount', 'discount_amount',
            'delivery_due_at', 'payment_due_at', 'confirmed_at', 'completed_at', 'cancelled_at',
            'rejected_at', 'updated_at',
        ]);

        return response()->json(['data' => $record]);
    }
}
