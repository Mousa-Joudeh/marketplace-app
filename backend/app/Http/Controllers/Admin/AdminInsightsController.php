<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShowAnalyticsRequest;
use App\Services\Admin\AnalyticsReport;
use App\Services\Admin\GenerateAdminInsights;
use Illuminate\Http\JsonResponse;

class AdminInsightsController extends Controller
{
    public function __invoke(
        ShowAnalyticsRequest $request,
        AnalyticsReport $analytics,
        GenerateAdminInsights $generate,
    ): JsonResponse {
        $report = $analytics->build($request->validated());
        $hasData = array_sum($report['totals']) > 0;

        return response()->json([
            'status' => $hasData ? 'generated' : 'no_data',
            'report' => $report,
            'insights' => $hasData ? $generate->generate($report) : null,
        ])->header('Cache-Control', 'no-store');
    }
}
