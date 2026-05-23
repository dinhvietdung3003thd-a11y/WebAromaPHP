<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InventoryTransactionService
{
    public function getAll(): array
    {
        return InventoryTransaction::query()
            ->with(['inventory', 'user'])
            ->orderByDesc('transaction_id')
            ->get()
            ->map(fn (InventoryTransaction $transaction): array => $this->mapTransaction($transaction))
            ->values()
            ->all();
    }

    public function create(array $payload, ?User $user): array
    {
        $transaction = DB::transaction(function () use ($payload, $user): InventoryTransaction {
            $inventory = Inventory::query()->lockForUpdate()->find($payload['inventoryId']);
            if (! $inventory) {
                throw new HttpException(404, 'Inventory not found');
            }

            $quantity = (float) $payload['quantity'];
            $currentStock = (float) $inventory->quantity_in_stock;
            $type = $payload['transactionType'];

            if ($type === 'Import') {
                $inventory->quantity_in_stock = $currentStock + $quantity;
            } else {
                if (($currentStock - $quantity) < 0) {
                    throw new HttpException(422, 'Export quantity exceeds available stock');
                }
                $inventory->quantity_in_stock = $currentStock - $quantity;
            }

            $inventory->save();

            return InventoryTransaction::query()->create([
                'inventory_id' => $payload['inventoryId'],
                'transaction_type' => $type,
                'quantity' => $quantity,
                'price' => $payload['price'] ?? 0,
                'transaction_date' => $payload['transactionDate'] ?? now(),
                'user_id' => $user?->user_id,
                'note' => $payload['note'] ?? null,
            ]);
        });

        $transaction->load(['inventory', 'user']);

        return $this->mapTransaction($transaction);
    }

    private function mapTransaction(InventoryTransaction $transaction): array
    {
        return [
            'transactionId' => (int) $transaction->transaction_id,
            'inventoryId' => (int) $transaction->inventory_id,
            'inventoryName' => $transaction->inventory?->name,
            'transactionType' => $transaction->transaction_type,
            'quantity' => (float) $transaction->quantity,
            'price' => (float) $transaction->price,
            'transactionDate' => optional($transaction->transaction_date)->format('Y-m-d\TH:i:s') ?? null,
            'userId' => $transaction->user_id !== null ? (int) $transaction->user_id : null,
            'note' => $transaction->note,
        ];
    }
}
