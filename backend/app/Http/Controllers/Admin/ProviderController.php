<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'status' => ['sometimes', 'required', 'in:pending'],
        ]);

        $providers = ProviderProfile::query()
            ->where('approval_status', $filters['status'] ?? 'pending')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15, ['id', 'display_name', 'city', 'approval_status', 'created_at'])
            ->appends($filters);

        return response()->json($providers);
    }
}