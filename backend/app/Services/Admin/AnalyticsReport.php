<?php

namespace App\Services\Admin;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class AnalyticsReport
{
    private const TIMEZONE = 'Asia/Amman';

    public function build(array $filters): array
    {
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

        return [
            'date_from' => $start->toDateString(),
            'date_to' => $end->toDateString(),
            'timezone' => self::TIMEZONE,
            'totals' => [
                'page_visits' => array_sum(array_column($daily, 'page_visits')),
                'new_bookings' => array_sum(array_column($daily, 'new_bookings')),
            ],
            'daily' => $daily,
        ];
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
