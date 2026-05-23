<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClientOrderService
{
    public function listForCustomer(Customer $customer): array
    {
        return Order::query()->with(['customer', 'table'])
            ->where('customer_id', $customer->customer_id)
            ->orderByDesc('order_id')
            ->get()
            ->map(fn (Order $o): array => $this->mapListItem($o))
            ->values()->all();
    }

    public function getDetailForCustomer(Customer $customer, int $id): array
    {
        $order = Order::query()->with(['customer', 'table', 'orderDetails.product'])
            ->where('customer_id', $customer->customer_id)
            ->find($id);

        if (! $order) {
            throw new HttpException(404, 'Order not found');
        }

        return $this->mapDetail($order);
    }

    public function createForCustomer(Customer $customer, array $payload): array
    {
        $order = DB::transaction(function () use ($customer, $payload) {
            [$detailRows, $total] = $this->buildDetailRows($payload['details']);

            $order = Order::query()->create([
                'customer_id' => $customer->customer_id,
                'table_id' => $payload['tableId'] ?? null,
                'status' => 'Pending',
                'note' => $payload['note'] ?? null,
                'total_amount' => $total,
            ]);

            foreach ($detailRows as $row) {
                OrderDetail::query()->create($row + ['order_id' => $order->order_id]);
            }

            return $order;
        });

        return $this->getDetailForCustomer($customer, (int) $order->order_id);
    }

    private function buildDetailRows(array $details): array
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
            if (! $product->is_available) {
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
