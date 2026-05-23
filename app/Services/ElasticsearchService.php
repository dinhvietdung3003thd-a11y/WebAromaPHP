<?php

namespace App\Services;

use App\Models\Product;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    private Client $client;

    private string $index;

    public function __construct()
    {
        $host = (string) env('ELASTICSEARCH_HOST', 'http://localhost:9200');
        $this->index = (string) env('ELASTICSEARCH_PRODUCT_INDEX', 'products');
        $this->client = ClientBuilder::create()->setHosts([$host])->build();
    }

    public function indexProduct(Product $product): void
    {
        $this->client->index([
            'index' => $this->index,
            'id' => (string) $product->product_id,
            'body' => $this->toDocument($product),
        ]);
    }

    public function updateProduct(Product $product): void
    {
        $this->indexProduct($product);
    }

    public function deleteProduct(int $productId): void
    {
        $exists = $this->client->exists([
            'index' => $this->index,
            'id' => (string) $productId,
        ])->asBool();

        if (! $exists) {
            return;
        }

        $this->client->delete([
            'index' => $this->index,
            'id' => (string) $productId,
        ]);
    }

    public function searchProducts(string $keyword): array
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            return [];
        }

        $response = $this->client->search([
            'index' => $this->index,
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $keyword,
                        'fields' => ['name^3', 'description', 'categoryName^2'],
                        'type' => 'best_fields',
                    ],
                ],
            ],
        ]);

        $hits = $response->asArray()['hits']['hits'] ?? [];

        return array_map(function (array $hit): array {
            $source = $hit['_source'] ?? [];

            return [
                'productId' => (int) ($source['productId'] ?? 0),
                'name' => $source['name'] ?? '',
                'price' => (float) ($source['price'] ?? 0),
                'imageUrl' => $source['imageUrl'] ?? null,
                'isAvailable' => (bool) ($source['isAvailable'] ?? false),
                'categoryId' => (int) ($source['categoryId'] ?? 0),
                'categoryName' => $source['categoryName'] ?? '',
                'description' => $source['description'] ?? null,
            ];
        }, $hits);
    }

    private function toDocument(Product $product): array
    {
        $product->loadMissing('category');

        return [
            'productId' => (int) $product->product_id,
            'name' => $product->name,
            'description' => $product->description,
            'categoryId' => (int) $product->category_id,
            'categoryName' => $product->category?->name,
            'price' => (float) $product->price,
            'imageUrl' => $product->image_url,
            'isAvailable' => (bool) $product->is_available,
        ];
    }
}
