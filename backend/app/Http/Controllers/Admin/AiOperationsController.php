<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShowAnalyticsRequest;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AiOperationsController extends Controller
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
        $fromUtc = $start->utc();
        $untilUtc = $end->addDay()->utc();

        $sessions = DB::table('ai_sessions')
            ->where('created_at', '>=', $fromUtc)
            ->where('created_at', '<', $untilUtc)
            ->select('tool_key', 'status')
            ->selectRaw('COUNT(*) AS session_count, SUM(input_tokens) AS input_tokens, SUM(output_tokens) AS output_tokens')
            ->groupBy('tool_key', 'status')
            ->orderByDesc('session_count')->orderBy('tool_key')->orderBy('status')
            ->get()
            ->map(fn (object $row): array => [
                'tool_key' => $row->tool_key,
                'status' => $row->status,
                'count' => (int) $row->session_count,
                'input_tokens' => $row->input_tokens === null ? null : (int) $row->input_tokens,
                'output_tokens' => $row->output_tokens === null ? null : (int) $row->output_tokens,
            ]);

        $subscriptions = DB::table('ai_subscriptions')
            ->where('starts_at', '>=', $fromUtc)
            ->where('starts_at', '<', $untilUtc)
            ->select('plan_code', 'status')
            ->selectRaw('COUNT(*) AS period_count')
            ->groupBy('plan_code', 'status')
            ->orderByDesc('period_count')->orderBy('plan_code')->orderBy('status')
            ->get()
            ->map(fn (object $row): array => [
                'plan_code' => $row->plan_code,
                'status' => $row->status,
                'count' => (int) $row->period_count,
            ]);

        return response()->json([
            'date_from' => $start->toDateString(),
            'date_to' => $end->toDateString(),
            'timezone' => self::TIMEZONE,
            'sessions' => [
                'count' => $sessions->sum('count'),
                'input_tokens' => $sessions->sum('input_tokens'),
                'output_tokens' => $sessions->sum('output_tokens'),
                'by_tool_and_status' => $sessions,
            ],
            'subscriptions_started' => [
                'count' => $subscriptions->sum('count'),
                'by_plan_and_status' => $subscriptions,
            ],
        ])->header('Cache-Control', 'no-store');
    }
}
