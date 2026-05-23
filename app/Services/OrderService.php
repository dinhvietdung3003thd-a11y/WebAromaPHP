<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderService
{
    public function listAll(): array
    {
        return Order::query()->with(['customer', 'table'])->orderByDesc('order_id')->get()
            ->map(fn (Order $o): array => $this->mapListItem($o))->values()->all();
    }

    public function getDetail(int $id): array
    {
        $order = Order::query()->with(['customer', 'table', 'orderDetails.product'])->find($id);
        if (! $order) {
            throw new HttpException(404, 'Order not found');
        }

        return $this->mapDetail($order);
    }

    public function createByStaff(User $user, array $payload): array
    {
        $order = DB::transaction(function () use ($user, $payload) {
            [$detailRows, $total] = $this->buildDetailRows($payload['details'], true);

            $order = Order::query()->create([
                'user_id' => $user->user_id,
                'customer_id' => $payload['customerId'] ?? null,
                'table_id' => $payload['tableId'] ?? null,
                'status' => $payload['status'] ?? 'Pending',
                'note' => $payload['note'] ?? null,
                'total_amount' => $total,
            ]);

            foreach ($detailRows as $row) {
                OrderDetail::query()->create($row + ['order_id' => $order->order_id]);
            }

            return $order;
        });

        return $this->getDetail((int) $order->order_id);
    }

    public function update(int $id, array $payload): array
    {
        DB::transaction(function () use ($id, $payload): void {
            $order = Order::query()->lockForUpdate()->find($id);
            if (! $order) {
                throw new HttpException(404, 'Order not found');
            }

            if (array_key_exists('tableId', $payload)) {
                $order->table_id = $payload['tableId'];
            }
            if (array_key_exists('note', $payload)) {
                $order->note = $payload['note'];
            }

            if (array_key_exists('details', $payload)) {
                [$detailRows, $total] = $this->buildDetailRows($payload['details'], true);

                OrderDetail::query()->where('order_id', $order->order_id)->delete();
                foreach ($detailRows as $row) {
                    OrderDetail::query()->create($row + ['order_id' => $order->order_id]);
                }
                $order->total_amount = $total;
            }

            $order->save();
        });

        return $this->getDetail($id);
    }

    public function updateStatus(int $id, string $targetStatus): array
    {
        DB::transaction(function () use ($id, $targetStatus): void {
            $order = Order::query()->lockForUpdate()->find($id);
            if (! $order) {
                throw new HttpException(404, 'Order not found');
            }

            $current = (string) $order->status;
            $allowed = [
                'Pending' => ['Completed', 'Cancelled'],
                'Completed' => [],
                'Cancelled' => [],
            ];

            if ($current !== $targetStatus && ! in_array($targetStatus, $allowed[$current] ?? [], true)) {
                throw new HttpException(422, 'Invalid status transition');
            }

            $order->status = $targetStatus;
            $order->save();
        });

        return $this->getDetail($id);
    }

    public function cancel(int $id): array
    {
        return $this->updateStatus($id, 'Cancelled');
    }

    private function buildDetailRows(array $details, bool $enforceAvailability): array
    {
        $productIds = collect($details)->pluck('productId')->map(fn ($id) => (int) $id)->values()->all();
        $products = Product::query()->whereIn('product_id', $productIds)->get()->keyBy('product_id');

        $rows = [];
        $total = 0.0;

        foreach ($details as $item) {
            $productId = (int) $item['productId'];
            $qty = (int) $item['quantity'];
            $product = $products->get($productId);

            if (! $product) {
                throw new HttpException(422, 'Invalid product in order details');
            }
            if ($enforceAvailability && ! $product->is_available) {
                throw new HttpException(422, 'Product is unavailable');
            }

            $unitPrice = (float) $product->price;
            $subtotal = $unitPrice * $qty;
            $total += $subtotal;

            $rows[] = [
                'product_id' => $productId,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ];
        }

        return [$rows, $total];
    }

    private function mapListItem(Order $order): array
    {
        return [
            'orderId' => (int) $order->order_id,
            'customerId' => $order->customer_id ? (int) $order->customer_id : null,
            'customerName' => $order->customer?->full_name,
            'tableId' => $order->table_id ? (int) $order->table_id : null,
            'status' => $order->status,
            'totalAmount' => (float) $order->total_amount,
            'note' => $order->note,
            'createdAt' => optional($order->created_at)->format('Y-m-d\TH:i:s') ?? null,
        ];
    }

    private function mapDetail(Order $order): array
    {
        return $this->mapListItem($order) + [
            'details' => $order->orderDetails->map(fn (OrderDetail $d): array => [
                'orderDetailId' => (int) $d->order_detail_id,
                'productId' => (int) $d->product_id,
                'productName' => $d->product?->name,
                'quantity' => (int) $d->quantity,
                'unitPrice' => (float) $d->unit_price,
                'subtotal' => (float) $d->subtotal,
            ])->values()->all(),
        ];
    }
}
