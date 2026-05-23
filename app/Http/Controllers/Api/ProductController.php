<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Services\ElasticsearchService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
        private readonly ElasticsearchService $elasticsearchService,
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->productService->getAll());
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->productService->getById($id));
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return response()->json($product, 201);
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        return response()->json($this->productService->update($id, $request->validated()));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productService->delete($id);

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function searchElastic(Request $request): JsonResponse
    {
        $keyword = $request->query('keyword', '');

        return response()->json($this->elasticsearchService->searchProducts((string) $keyword));
    }
}
