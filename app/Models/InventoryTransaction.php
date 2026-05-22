<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    protected $table = 'inventory_transactions';

    protected $primaryKey = 'transaction_id';

    public $timestamps = false;

    protected $fillable = [
        'inventory_id',
        'transaction_type',
        'quantity',
        'price',
        'transaction_date',
        'user_id',
        'note',
    ];

    protected $casts = [
        'inventory_id' => 'integer',
        'quantity' => 'decimal:2',
        'price' => 'decimal:2',
        'transaction_date' => 'datetime',
        'user_id' => 'integer',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'inventory_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
