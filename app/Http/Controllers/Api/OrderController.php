<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\CreateOrderRequest;
use App\Http\Requests\Orders\UpdateOrderRequest;
use App\Http\Requests\Orders\UpdateOrderStatusRequest;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $service)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->service->listAll());
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->service->getDetail($id));
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        return response()->json($this->service->createByStaff($this->resolveUser($request), $request->validated()), 201);
    }

    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        return response()->json($this->service->update($id, $request->validated()));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        return response()->json($this->service->updateStatus($id, $request->string('status')->toString()));
    }

    public function destroy(int $id): JsonResponse
    {
        return response()->json($this->service->cancel($id));
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
