<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\UpdateUserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListUsersRequest;
use App\Http\Requests\Admin\UpdateUserStatusRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    private const LIST_FIELDS = ['id', 'name', 'email', 'city', 'role', 'status', 'created_at'];

    public function index(ListUsersRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $query = User::query()->where(array_intersect_key($filters, ['role' => true, 'status' => true]));

        if (isset($filters['search'])) {
            $pattern = '%'.strtr($filters['search'], ['!' => '!!', '%' => '!%', '_' => '!_']).'%';

            $query->where(function (Builder $user) use ($pattern): void {
                $user->whereRaw("name LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("email LIKE ? ESCAPE '!'", [$pattern]);
            });
        }

        $users = $query->orderByDesc('created_at')->orderByDesc('id')
            ->paginate(15, self::LIST_FIELDS)
            ->appends($filters);

        return response()->json($users);
    }

    public function show(string $user): JsonResponse
    {
        $account = User::query()->findOrFail($user, [...self::LIST_FIELDS, 'email_verified_at']);
        $providerProfile = null;

        if ($account->role === 'provider') {
            $account->load('providerProfile:id,user_id,approval_status');
            $providerProfile = $account->providerProfile?->only(['id', 'approval_status']);
        }

        return response()->json([
            'data' => [
                ...$account->only([...self::LIST_FIELDS, 'email_verified_at']),
                'provider_profile' => $providerProfile,
            ],
        ]);
    }

    public function updateStatus(
        UpdateUserStatusRequest $request,
        UpdateUserStatus $update,
        string $user,
    ): JsonResponse {
        $change = $request->validated();
        $account = $update->execute(
            $user,
            $request->user()->getAuthIdentifier(),
            $change['status'],
            $change['reason'],
        );

        return response()->json(['data' => $account->only(['id', 'status'])]);
    }
}
