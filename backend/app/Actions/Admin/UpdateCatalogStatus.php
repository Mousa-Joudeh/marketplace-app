<?php

namespace App\Actions\Admin;

use App\Models\Package;
use App\Models\Service;
use App\Models\Space;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateCatalogStatus
{
    /** @param class-string<Space|Service|Package> $model */
    public function execute(string $model, string $itemId, int $adminId, string $status, ?string $reason): Space|Service|Package
    {
        [$listing, $previousStatus] = DB::transaction(function () use ($model, $itemId, $status): array {
            $listing = $model::query()->lockForUpdate()->findOrFail($itemId);
            $previousStatus = $listing->status;
            $allowed = match ($status) {
                'published' => in_array($previousStatus, ['draft', 'hidden'], true),
                'hidden' => $previousStatus === 'published',
                default => false,
            };

            abort_unless($allowed, 409, 'This catalog status transition is not allowed.');

            if ($status === 'published') {
                $this->ensurePublishable($listing);
            }

            $listing->status = $status;
            $listing->save();

            return [$listing, $previousStatus];
        }, 3);

        try {
            Log::channel('single')->info('Catalog status changed', [
                'admin_id' => $adminId,
                'type' => $listing->getTable(),
                'item_id' => $listing->getKey(),
                'previous_status' => $previousStatus,
                'new_status' => $status,
                'reason' => $reason,
            ]);
        } catch (Throwable) {
            error_log("Could not log catalog status change for {$listing->getTable()}/{$listing->getKey()} by admin {$adminId}.");
        }

        return $listing;
    }

    private function ensurePublishable(Space|Service|Package $listing): void
    {
        $provider = $listing->providerProfile()->sharedLock()->first();
        abort_unless($provider?->approval_status === 'approved', 409, 'The provider must be approved before publishing.');

        $account = $provider->user()->sharedLock()->first();
        abort_unless($account?->status === 'active', 409, 'The provider account must be active before publishing.');

        $category = $listing->category()->sharedLock()->first();
        abort_unless($category?->is_active, 409, 'The category must be active before publishing.');
    }
}
