<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function getAll(): array
    {
        return Category::query()
            ->orderBy('category_id')
            ->get(['category_id', 'name', 'description'])
            ->map(fn (Category $category): array => [
                'categoryId' => (int) $category->category_id,
                'name' => $category->name,
                'description' => $category->description,
            ])
            ->values()
            ->all();
    }
}
