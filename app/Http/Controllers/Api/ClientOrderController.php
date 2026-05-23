<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\CreateClientOrderRequest;
use App\Models\Customer;
use App\Services\ClientOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClientOrderController extends Controller
{
    public function __construct(private readonly ClientOrderService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $customer = $this->resolveCustomer($request);

        return response()->json($this->service->listForCustomer($customer));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $customer = $this->resolveCustomer($request);

        return response()->json($this->service->getDetailForCustomer($customer, $id));
    }

    public function store(CreateClientOrderRequest $request): JsonResponse
    {
        $customer = $this->resolveCustomer($request);

        return response()->json($this->service->createForCustomer($customer, $request->validated()), 201);
    }

    private function resolveCustomer(Request $request): Customer
    {
        $user = $request->attributes->get('auth_user');
        if (! $user instanceof Customer) {
            throw new HttpException(403, 'Forbidden');
        }

        return $user;
    }
}
