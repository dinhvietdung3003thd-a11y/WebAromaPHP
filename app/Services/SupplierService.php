<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SupplierService
{
    public function getAll(): array
    {
        return Supplier::query()->orderBy('supplier_id')->get()
            ->map(fn (Supplier $supplier): array => $this->mapSupplier($supplier))->values()->all();
    }

    public function getById(int $id): array
    {
        $supplier = Supplier::query()->find($id);
        if (! $supplier) {
            throw new HttpException(404, 'Supplier not found');
        }

        return $this->mapSupplier($supplier);
    }

    public function create(array $payload): array
    {
        $supplier = Supplier::query()->create($this->dbPayload($payload));

        return $this->mapSupplier($supplier);
    }

    public function update(int $id, array $payload): array
    {
        $supplier = Supplier::query()->find($id);
        if (! $supplier) {
            throw new HttpException(404, 'Supplier not found');
        }

        $supplier->fill($this->dbPayload($payload));
        $supplier->save();

        return $this->mapSupplier($supplier);
    }

    public function delete(int $id): void
    {
        $supplier = Supplier::query()->find($id);
        if (! $supplier) {
            throw new HttpException(404, 'Supplier not found');
        }

        try {
            $supplier->delete();
        } catch (QueryException) {
            throw new HttpException(409, 'Cannot delete supplier because it is referenced by other records');
        }
    }

    private function dbPayload(array $payload): array
    {
        $mapped = [];
        foreach (['name', 'phone', 'email', 'address'] as $field) {
            if (array_key_exists($field, $payload)) {
                $mapped[$field] = $payload[$field];
            }
        }

        if (array_key_exists('contactName', $payload)) {
            $mapped['contact_name'] = $payload['contactName'];
        }

        return $mapped;
    }

    private function mapSupplier(Supplier $supplier): array
    {
        return [
            'supplierId' => (int) $supplier->supplier_id,
            'name' => $supplier->name,
            'contactName' => $supplier->contact_name,
            'phone' => $supplier->phone,
            'email' => $supplier->email,
            'address' => $supplier->address,
        ];
    }
}
