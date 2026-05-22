<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $table = 'suppliers';

    protected $primaryKey = 'supplier_id';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'contact_name',
        'phone',
        'email',
        'address',
    ];

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(Inventory::class, 'supplier_id', 'supplier_id');
    }
}
