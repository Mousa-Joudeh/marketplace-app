<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListPaymentsRequest;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    private const LIST_FIELDS = [
        'id', 'booking_id', 'reference', 'method', 'status', 'transaction_type',
        'amount', 'currency', 'paid_at', 'created_at',
    ];

    public function index(ListPaymentsRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $query = Payment::query()
            ->with('booking:id,reference')
            ->where(array_intersect_key($filters, ['method' => true, 'status' => true, 'transaction_type' => true]));

        if (isset($filters['search'])) {
            $pattern = '%'.strtr($filters['search'], ['!' => '!!', '%' => '!%', '_' => '!_']).'%';

            $query->where(function (Builder $payment) use ($pattern): void {
                $payment->whereRaw("payments.reference LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereHas('booking', fn (Builder $booking) => $booking->whereRaw("bookings.reference LIKE ? ESCAPE '!'", [$pattern]));
            });
        }

        $payments = $query->orderByDesc('created_at')->orderByDesc('id')
            ->paginate(15, self::LIST_FIELDS)
            ->appends($filters);

        return response()->json($payments);
    }

    public function show(string $payment): JsonResponse
    {
        $record = Payment::query()
            ->with(['booking:id,reference', 'parentPayment:id,reference'])
            ->findOrFail($payment, [
                ...self::LIST_FIELDS,
                'parent_payment_id', 'processed_at', 'updated_at',
            ]);

        return response()->json(['data' => $record]);
    }
}
