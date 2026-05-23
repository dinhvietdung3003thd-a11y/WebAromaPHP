<?php

namespace App\Services;

use App\Models\Product;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProductService
{
    public function __construct(private readonly ElasticsearchService $elasticsearchService)
    {
    }

    public function getAll(): array
    {
        return Product::query()
            ->with('category')
            ->orderBy('product_id')
            ->get()
            ->map(fn (Product $product): array => $this->mapProduct($product))
            ->values()
            ->all();
    }

    public function getById(int $id): array
    {
        $product = Product::query()->with('category')->find($id);

        if (! $product) {
            throw new HttpException(404, 'Product not found');
        }

        return $this->mapProduct($product);
    }

    public function create(array $payload): array
    {
        $product = Product::query()->create($this->dbPayload($payload));
        $product->load('category');
        $this->elasticsearchService->indexProduct($product);

        return $this->mapProduct($product);
    }

    public function update(int $id, array $payload): array
    {
        $product = Product::query()->with('category')->find($id);

        if (! $product) {
            throw new HttpException(404, 'Product not found');
        }

        $product->fill($this->dbPayload($payload));
        $product->save();
        $product->load('category');

        $this->elasticsearchService->updateProduct($product);

        return $this->mapProduct($product);
    }

    public function delete(int $id): void
    {
        $product = Product::query()->find($id);

        if (! $product) {
            throw new HttpException(404, 'Product not found');
        }

        $productId = (int) $product->product_id;
        $product->delete();
        $this->elasticsearchService->deleteProduct($productId);
    }

    private function dbPayload(array $payload): array
    {
        $mapped = [];

        if (array_key_exists('name', $payload)) {
            $mapped['name'] = $payload['name'];
        }

        if (array_key_exists('price', $payload)) {
            $mapped['price'] = $payload['price'];
        }

        if (array_key_exists('categoryId', $payload)) {
            $mapped['category_id'] = $payload['categoryId'];
        }

        if (array_key_exists('imageUrl', $payload)) {
            $mapped['image_url'] = $payload['imageUrl'];
        }

        if (array_key_exists('description', $payload)) {
            $mapped['description'] = $payload['description'];
        }

        if (array_key_exists('isAvailable', $payload)) {
            $mapped['is_available'] = $payload['isAvailable'];
        }

        return $mapped;
    }

    public function mapProduct(Product $product): array
    {
        return [
            'productId' => (int) $product->product_id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'imageUrl' => $product->image_url,
            'isAvailable' => (bool) $product->is_available,
            'categoryId' => (int) $product->category_id,
            'categoryName' => $product->category?->name,
            'description' => $product->description,
        ];
    }
}
