<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListCatalogRequest;
use App\Models\Package;
use App\Models\Service;
use App\Models\Space;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class CatalogController extends Controller
{
    public const MODELS = [
        'spaces' => Space::class,
        'services' => Service::class,
        'packages' => Package::class,
    ];

    private const LIST_FIELDS = ['id', 'title', 'slug', 'status', 'provider_profile_id', 'category_id', 'created_at'];

    private const RELATIONS = ['providerProfile:id,display_name', 'category:id,name,slug'];

    public function index(ListCatalogRequest $request, string $type): JsonResponse
    {
        $model = self::MODELS[$type];
        $filters = $request->validated();
        $query = $model::query()->with(self::RELATIONS);

        foreach (['status' => 'status', 'provider_id' => 'provider_profile_id', 'category_id' => 'category_id'] as $filter => $column) {
            if (isset($filters[$filter])) {
                $query->where($column, $filters[$filter]);
            }
        }

        if (isset($filters['search'])) {
            $pattern = '%'.strtr($filters['search'], ['!' => '!!', '%' => '!%', '_' => '!_']).'%';

            $query->where(function (Builder $listing) use ($pattern): void {
                $listing->whereRaw("title LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("slug LIKE ? ESCAPE '!'", [$pattern]);
            });
        }

        $fields = [...self::LIST_FIELDS, ...$this->priceFields($type)];
        $listings = $query->orderByDesc('created_at')->orderByDesc('id')->paginate(15, $fields)->appends($filters);

        return response()->json($listings);
    }

    public function show(string $type, string $item): JsonResponse
    {
        $model = self::MODELS[$type];
        $fields = [...self::LIST_FIELDS, ...$this->priceFields($type), 'description', 'updated_at', ...$this->detailFields($type)];
        $listing = $model::query()->with(self::RELATIONS)->findOrFail($item, $fields);
        $images = $listing->catalogImages()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'storage_key', 'thumbnail_storage_key', 'sort_order', 'is_cover', 'alt_text', 'caption', 'width_px', 'height_px']);

        return response()->json([
            'data' => [
                ...$listing->toArray(),
                'images' => $images,
            ],
        ]);
    }

    private function priceFields(string $type): array
    {
        return match ($type) {
            'spaces' => ['hourly_rate'],
            'services' => ['price', 'pricing_unit'],
            'packages' => ['price'],
        };
    }

    private function detailFields(string $type): array
    {
        return match ($type) {
            'spaces' => [
                'address', 'city', 'district', 'capacity', 'area_sqm', 'amenities', 'house_rules',
                'min_booking_minutes', 'max_booking_minutes', 'booking_buffer_minutes',
            ],
            'services' => [
                'service_kind', 'location_mode', 'is_addon_only', 'space_id', 'deliverables', 'scope_limits',
                'client_requirements', 'estimated_duration_minutes', 'min_booking_minutes', 'max_booking_minutes',
                'booking_lead_time_hours', 'delivery_turnaround_days', 'revision_limit',
            ],
            'packages' => [
                'deliverables', 'scope_limits', 'client_requirements', 'session_duration_minutes', 'max_participants',
                'booking_lead_time_hours', 'delivery_turnaround_days', 'revision_limit', 'bookable_from', 'bookable_until',
            ],
        };
    }
}
