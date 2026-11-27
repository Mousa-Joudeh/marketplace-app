<?php

namespace App\Actions\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateUserStatus
{
    public function execute(string $userId, int $adminId, string $status, string $reason): User
    {
        [$account, $previousStatus] = DB::transaction(function () use ($userId, $status): array {
            $account = User::query()->lockForUpdate()->findOrFail($userId);

            abort_unless(in_array($account->role, ['customer', 'provider'], true), 409, 'Only customer and provider accounts can be managed here.');

            $previousStatus = $account->status;
            abort_if($previousStatus === $status, 409, 'The account already has this status.');

            $account->status = $status;
            $account->save();

            return [$account, $previousStatus];
        }, 3);

        try {
            Log::channel('single')->info('User account status changed', [
                'admin_id' => $adminId,
                'user_id' => $account->getKey(),
                'previous_status' => $previousStatus,
                'new_status' => $status,
                'reason' => $reason,
            ]);
        } catch (Throwable) {
            error_log("Could not log account status change for user {$account->getKey()} by admin {$adminId}.");
        }

        return $account;
    }
}
