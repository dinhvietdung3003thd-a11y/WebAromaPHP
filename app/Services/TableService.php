<?php

namespace App\Services;

use App\Models\Table;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TableService
{
    public function getAll(): array
    {
        return Table::query()->orderBy('table_id')->get()
            ->map(fn (Table $table): array => $this->mapTable($table))->values()->all();
    }

    public function getById(int $id): array
    {
        $table = Table::query()->find($id);
        if (! $table) {
            throw new HttpException(404, 'Table not found');
        }

        return $this->mapTable($table);
    }

    public function create(array $payload): array
    {
        $table = Table::query()->create($this->dbPayload($payload));

        return $this->mapTable($table);
    }

    public function update(int $id, array $payload): array
    {
        $table = Table::query()->find($id);
        if (! $table) {
            throw new HttpException(404, 'Table not found');
        }

        $table->fill($this->dbPayload($payload));
        $table->save();

        return $this->mapTable($table);
    }

    public function delete(int $id): void
    {
        $table = Table::query()->find($id);
        if (! $table) {
            throw new HttpException(404, 'Table not found');
        }

        try {
            $table->delete();
        } catch (QueryException) {
            throw new HttpException(409, 'Cannot delete table because it is referenced by other records');
        }
    }

    private function dbPayload(array $payload): array
    {
        $mapped = [];
        if (array_key_exists('name', $payload)) {
            $mapped['name'] = $payload['name'];
        }
        if (array_key_exists('status', $payload)) {
            $mapped['status'] = $payload['status'];
        }

        return $mapped;
    }

    private function mapTable(Table $table): array
    {
        return [
            'tableId' => (int) $table->table_id,
            'name' => $table->name,
            'status' => $table->status,
        ];
    }
}
