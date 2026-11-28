<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShowAnalyticsRequest;
use App\Services\Admin\AnalyticsReport;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    public function __invoke(ShowAnalyticsRequest $request, AnalyticsReport $analytics): JsonResponse
    {
        return response()->json($analytics->build($request->validated()))
            ->header('Cache-Control', 'no-store');
    }
}
