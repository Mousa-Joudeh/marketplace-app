<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShowAnalyticsRequest;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    private const TIMEZONE = 'Asia/Amman';

    public function __invoke(ShowAnalyticsRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $today = CarbonImmutable::today(self::TIMEZONE);
        $start = isset($filters['date_from'])
            ? CarbonImmutable::parse($filters['date_from'], self::TIMEZONE)
            : $today->subDays(29);
        $end = isset($filters['date_to'])
            ? CarbonImmutable::parse($filters['date_to'], self::TIMEZONE)
            : $today;

        $days = [];
        for ($day = $start; $day->lessThanOrEqualTo($end); $day = $day->addDay()) {
            $days[] = $day;
        }

        $visits = $this->dailyCounts('page_visits', 'visited_at', $days);
        $bookings = $this->dailyCounts('bookings', 'created_at', $days);
        $daily = [];

        foreach ($days as $day) {
            $date = $day->toDateString();
            $daily[] = [
                'date' => $date,
                'page_visits' => (int) ($visits[$date] ?? 0),
                'new_bookings' => (int) ($bookings[$date] ?? 0),
            ];
        }

        return response()->json([
            'date_from' => $start->toDateString(),
            'date_to' => $end->toDateString(),
            'timezone' => self::TIMEZONE,
            'totals' => [
                'page_visits' => array_sum(array_column($daily, 'page_visits')),
                'new_bookings' => array_sum(array_column($daily, 'new_bookings')),
            ],
            'daily' => $daily,
        ])->header('Cache-Control', 'no-store');
    }

    /** @param list<CarbonImmutable> $days */
    private function dailyCounts(string $table, string $timestamp, array $days): array
    {
        $cases = [];
        $bindings = [];

        // Use UTC cutoffs for local midnights without relying on MySQL timezone tables.
        foreach ($days as $day) {
            $cases[] = "WHEN {$timestamp} < ? THEN ?";
            $bindings[] = $day->addDay()->utc()->toDateTimeString();
            $bindings[] = $day->toDateString();
        }

        return DB::table($table)
            ->where($timestamp, '>=', $days[0]->utc())
            ->where($timestamp, '<', $days[array_key_last($days)]->addDay()->utc())
            ->selectRaw('CASE '.implode(' ', $cases).' END AS local_date, COUNT(*) AS total', $bindings)
            ->groupBy('local_date')
            ->pluck('total', 'local_date')
            ->all();
    }
}
