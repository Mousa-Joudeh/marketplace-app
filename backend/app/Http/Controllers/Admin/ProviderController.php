<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\ReviewProvider;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListProvidersRequest;
use App\Http\Requests\Admin\ReviewProviderRequest;
use App\Models\ProviderProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class ProviderController extends Controller
{
    public function index(ListProvidersRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $approvalStatus = $filters['status'] ?? 'pending';
        $accountStatus = $filters['account_status'] ?? null;
        $search = $filters['search'] ?? null;

        $query = ProviderProfile::query();

        if ($approvalStatus !== 'all') {
            $query->where('approval_status', $approvalStatus);
        }

        if ($accountStatus !== null) {
            $query->whereHas('user', fn (Builder $user) => $user->where('status', $accountStatus));
        }

        if ($search !== null) {
            $pattern = '%'.strtr($search, ['!' => '!!', '%' => '!%', '_' => '!_']).'%';

            $query->where(function (Builder $profile) use ($pattern): void {
                $profile->whereRaw("display_name LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("city LIKE ? ESCAPE '!'", [$pattern]);
            });
        }

        $providers = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15, ['id', 'display_name', 'city', 'approval_status', 'created_at'])
            ->appends($filters);

        return response()->json($providers);
    }

    public function show(string $provider): JsonResponse
    {
        $profile = ProviderProfile::query()->findOrFail($provider);

        return response()->json([
            'data' => $profile->only([
                'id',
                'display_name',
                'slug',
                'city',
                'district',
                'public_address',
                'tagline',
                'bio',
                'logo_path',
                'cover_path',
                'instagram_url',
                'approval_status',
                'created_at',
            ]),
        ]);
    }

    public function review(ReviewProviderRequest $request, ReviewProvider $review, string $provider): JsonResponse
    {
        $decision = $request->validated();
        $profile = $review->execute(
            $provider,
            $request->user()->getAuthIdentifier(),
            $decision['status'],
            $decision['reason'],
        );

        return response()->json([
            'data' => $profile->only(['id', 'approval_status']),
        ]);
    }
}
