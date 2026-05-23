<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('elastic:sync-products', function () {
    $products = \App\Models\Product::query()->with('category')->get();

    /** @var \App\Services\ElasticsearchService $elasticsearchService */
    $elasticsearchService = app(\App\Services\ElasticsearchService::class);

    $indexed = 0;

    foreach ($products as $product) {
        $elasticsearchService->indexProduct($product);
        $indexed++;
    }

    $indexName = (string) config('elasticsearch.product_index', 'products');
    $this->info("Indexed {$indexed} products into Elasticsearch index '{$indexName}'.");
})->purpose('Sync products from MySQL to Elasticsearch');
