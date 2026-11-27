<?php

namespace App\Actions\Admin;

use App\Models\ProviderProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReviewProvider
{
    public function execute(string $providerId, int $adminId, string $status, string $reason): ProviderProfile
    {
        $profile = DB::transaction(function () use ($providerId, $status): ProviderProfile {
            $profile = ProviderProfile::query()->lockForUpdate()->findOrFail($providerId);

            abort_if($profile->approval_status !== 'pending', 409, 'Only pending providers can be reviewed.');

            $profile->approval_status = $status;
            $profile->save();

            return $profile;
        });

        try {
            Log::channel('single')->info('Provider approval decision', [
                'admin_id' => $adminId,
                'provider_id' => $profile->getKey(),
                'previous_status' => 'pending',
                'new_status' => $status,
                'reason' => $reason,
            ]);
        } catch (Throwable) {
            error_log("Could not log provider approval decision for provider {$profile->getKey()} by admin {$adminId}.");
        }

        return $profile;
    }
}
