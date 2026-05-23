<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryTransaction\StoreInventoryTransactionRequest;
use App\Models\User;
use App\Services\InventoryTransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InventoryTransactionController extends Controller
{
    public function __construct(private readonly InventoryTransactionService $service)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->service->getAll());
    }

    public function store(StoreInventoryTransactionRequest $request): JsonResponse
    {
        return response()->json($this->service->create($request->validated(), $this->resolveUser($request)), 201);
    }

    private function resolveUser(Request $request): User
    {
        $user = $request->attributes->get('auth_user');

        if (! $user instanceof User) {
            throw new HttpException(403, 'Forbidden');
        }

        return $user;
    }
}
