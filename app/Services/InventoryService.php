<?php

namespace App\Services;

use App\Models\Inventory;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InventoryService
{
    public function getAll(): array
    {
        return Inventory::query()
            ->with('supplier')
            ->orderBy('inventory_id')
            ->get()
            ->map(fn (Inventory $inventory): array => $this->mapInventory($inventory))
            ->values()
            ->all();
    }

    public function getById(int $id): array
    {
        $inventory = Inventory::query()->with('supplier')->find($id);

        if (! $inventory) {
            throw new HttpException(404, 'Inventory not found');
        }

        return $this->mapInventory($inventory);
    }

    public function create(array $payload): array
    {
        $inventory = Inventory::query()->create($this->dbPayload($payload));
        $inventory->load('supplier');

        return $this->mapInventory($inventory);
    }

    public function update(int $id, array $payload): array
    {
        $inventory = Inventory::query()->with('supplier')->find($id);

        if (! $inventory) {
            throw new HttpException(404, 'Inventory not found');
        }

        $inventory->fill($this->dbPayload($payload));
        $inventory->save();
        $inventory->load('supplier');

        return $this->mapInventory($inventory);
    }

    public function delete(int $id): void
    {
        $inventory = Inventory::query()->find($id);

        if (! $inventory) {
            throw new HttpException(404, 'Inventory not found');
        }

        try {
            $inventory->delete();
        } catch (QueryException) {
            throw new HttpException(409, 'Cannot delete inventory because it is referenced by other records');
        }
    }

    private function dbPayload(array $payload): array
    {
        $mapped = [];

        if (array_key_exists('name', $payload)) {
            $mapped['name'] = $payload['name'];
        }
        if (array_key_exists('unit', $payload)) {
            $mapped['unit'] = $payload['unit'];
        }
        if (array_key_exists('quantityInStock', $payload)) {
            $mapped['quantity_in_stock'] = $payload['quantityInStock'];
        }
        if (array_key_exists('minThreshold', $payload)) {
            $mapped['min_threshold'] = $payload['minThreshold'];
        }
        if (array_key_exists('supplierId', $payload)) {
            $mapped['supplier_id'] = $payload['supplierId'];
        }

        return $mapped;
    }

    private function mapInventory(Inventory $inventory): array
    {
        return [
            'inventoryId' => (int) $inventory->inventory_id,
            'name' => $inventory->name,
            'unit' => $inventory->unit,
            'quantityInStock' => (float) $inventory->quantity_in_stock,
            'minThreshold' => (float) $inventory->min_threshold,
            'supplierId' => $inventory->supplier_id !== null ? (int) $inventory->supplier_id : null,
            'supplierName' => $inventory->supplier?->name,
            'updatedAt' => optional($inventory->updated_at)->format('Y-m-d\TH:i:s') ?? null,
        ];
    }
}
