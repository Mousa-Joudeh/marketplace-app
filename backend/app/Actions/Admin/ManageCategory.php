<?php

namespace App\Actions\Admin;

use App\Models\Category;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ManageCategory
{
    public function save(array $attributes, ?string $categoryId = null): Category
    {
        try {
            return DB::transaction(function () use ($attributes, $categoryId): Category {
                $category = $categoryId === null
                    ? new Category
                    : Category::query()->lockForUpdate()->findOrFail($categoryId);

                $this->validateUniqueFields($category, $attributes);
                $category->fill(Arr::except($attributes, ['parent_id']));

                if (array_key_exists('parent_id', $attributes)) {
                    $parentId = $attributes['parent_id'];
                    $category->parent_id = $parentId === null ? null : (int) $parentId;
                }

                $this->validateHierarchy($category);

                if ($category->exists && $category->isDirty('is_active') && ! $category->is_active) {
                    $this->ensureUnused($category);
                }

                $category->save();

                return $category->refresh();
            }, 3);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['category' => 'A category with this name or slug already exists.']);
        }
    }

    public function delete(string $categoryId): void
    {
        DB::transaction(function () use ($categoryId): void {
            $category = Category::query()->lockForUpdate()->findOrFail($categoryId);

            $this->ensureUnused($category);
            $category->delete();
        }, 3);
    }

    private function validateUniqueFields(Category $category, array $attributes): void
    {
        $name = Rule::unique(Category::class, 'name');
        $slug = Rule::unique(Category::class, 'slug');

        if ($category->exists) {
            $name->ignore($category);
            $slug->ignore($category);
        }

        Validator::make($attributes, [
            'name' => ['sometimes', $name],
            'slug' => ['sometimes', $slug],
        ])->validate();
    }

    private function validateHierarchy(Category $category): void
    {
        $parentId = $category->parent_id;
        $parentChanged = $category->isDirty('parent_id');
        $activating = $category->isDirty('is_active') && $category->is_active;

        if ($parentId === null || (! $parentChanged && ! $activating)) {
            return;
        }

        if ((int) $category->getKey() === (int) $parentId) {
            throw ValidationException::withMessages(['parent_id' => 'A category cannot be its own parent.']);
        }

        $parent = Category::query()->lockForUpdate()->find($parentId);

        if ($parent === null || $parent->parent_id !== null || ! $parent->is_active) {
            throw ValidationException::withMessages(['parent_id' => 'Choose an active top-level category as the parent.']);
        }

        if ($parentChanged && $category->exists && $category->children()->withTrashed()->exists()) {
            throw ValidationException::withMessages(['parent_id' => 'A category with children must stay at the top level.']);
        }
    }

    private function ensureUnused(Category $category): void
    {
        abort_if(
            $category->children()->withTrashed()->exists()
            || $category->spaces()->withTrashed()->exists()
            || $category->services()->withTrashed()->exists()
            || $category->packages()->withTrashed()->exists()
            || $category->equipment()->withTrashed()->exists(),
            409,
            'Reassign the linked categories and catalog items before disabling or deleting this category.',
        );
    }
}
