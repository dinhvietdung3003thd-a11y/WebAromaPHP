<?php

namespace App\Services;

use App\Models\Recipe;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RecipeService
{
    public function getAll(): array
    {
        return Recipe::query()->with(['product', 'inventory'])->orderBy('recipe_id')->get()
            ->map(fn (Recipe $recipe): array => $this->mapRecipe($recipe))->values()->all();
    }

    public function getById(int $id): array
    {
        $recipe = Recipe::query()->with(['product', 'inventory'])->find($id);
        if (! $recipe) {
            throw new HttpException(404, 'Recipe not found');
        }

        return $this->mapRecipe($recipe);
    }

    public function create(array $payload): array
    {
        try {
            $recipe = Recipe::query()->create($this->dbPayload($payload));
        } catch (QueryException) {
            throw new HttpException(422, 'Recipe for this product and inventory already exists');
        }

        $recipe->load(['product', 'inventory']);

        return $this->mapRecipe($recipe);
    }

    public function update(int $id, array $payload): array
    {
        $recipe = Recipe::query()->find($id);
        if (! $recipe) {
            throw new HttpException(404, 'Recipe not found');
        }

        try {
            $recipe->fill($this->dbPayload($payload));
            $recipe->save();
        } catch (QueryException) {
            throw new HttpException(422, 'Recipe for this product and inventory already exists');
        }

        $recipe->load(['product', 'inventory']);

        return $this->mapRecipe($recipe);
    }

    public function delete(int $id): void
    {
        $recipe = Recipe::query()->find($id);
        if (! $recipe) {
            throw new HttpException(404, 'Recipe not found');
        }

        try {
            $recipe->delete();
        } catch (QueryException) {
            throw new HttpException(409, 'Cannot delete recipe because it is referenced by other records');
        }
    }

    private function dbPayload(array $payload): array
    {
        $mapped = [];
        if (array_key_exists('productId', $payload)) {
            $mapped['product_id'] = $payload['productId'];
        }
        if (array_key_exists('inventoryId', $payload)) {
            $mapped['inventory_id'] = $payload['inventoryId'];
        }
        if (array_key_exists('quantityNeeded', $payload)) {
            $mapped['quantity_needed'] = $payload['quantityNeeded'];
        }

        return $mapped;
    }

    private function mapRecipe(Recipe $recipe): array
    {
        return [
            'recipeId' => (int) $recipe->recipe_id,
            'productId' => (int) $recipe->product_id,
            'productName' => $recipe->product?->name,
            'inventoryId' => (int) $recipe->inventory_id,
            'inventoryName' => $recipe->inventory?->name,
            'quantityNeeded' => (float) $recipe->quantity_needed,
        ];
    }
}
