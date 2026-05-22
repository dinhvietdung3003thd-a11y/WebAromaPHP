<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recipe extends Model
{
    protected $table = 'recipes';

    protected $primaryKey = 'recipe_id';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'inventory_id',
        'quantity_needed',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'inventory_id' => 'integer',
        'quantity_needed' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'inventory_id');
    }
}
