<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $primaryKey = 'inventory_id';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'unit',
        'quantity_in_stock',
        'min_threshold',
        'supplier_id',
        'updated_at',
    ];

    protected $casts = [
        'quantity_in_stock' => 'decimal:2',
        'min_threshold' => 'decimal:2',
        'supplier_id' => 'integer',
        'updated_at' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class, 'inventory_id', 'inventory_id');
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'inventory_id', 'inventory_id');
    }
}
