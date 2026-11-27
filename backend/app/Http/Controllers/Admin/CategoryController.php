<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListCategoriesRequest;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(ListCategoriesRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $status = $filters['status'] ?? null;
        $parentId = $filters['parent_id'] ?? null;
        $search = $filters['search'] ?? null;

        $query = Category::query();

        if ($status !== null) {
            $query->where('is_active', $status === 'active');
        }

        if ($parentId === 'root') {
            $query->whereNull('parent_id');
        } elseif ($parentId !== null) {
            $query->where('parent_id', $parentId);
        }

        if ($search !== null) {
            $pattern = '%'.strtr($search, ['!' => '!!', '%' => '!%', '_' => '!_']).'%';

            $query->where(function (Builder $category) use ($pattern): void {
                $category->whereRaw("name LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("slug LIKE ? ESCAPE '!'", [$pattern]);
            });
        }

        $categories = $query
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15, ['id', 'name', 'slug', 'parent_id', 'is_active', 'sort_order'])
            ->appends($filters);

        return response()->json($categories);
    }

    public function show(string $category): JsonResponse
    {
        $record = Category::query()->with('parent:id,name,slug')->findOrFail($category);

        return response()->json([
            'data' => $record->only([
                'id',
                'name',
                'slug',
                'parent_id',
                'parent',
                'description',
                'icon_key',
                'cover_image_path',
                'is_active',
                'sort_order',
                'created_at',
                'updated_at',
            ]),
        ]);
    }
}
