<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    protected $table = 'tables';

    protected $primaryKey = 'table_id';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'status',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id', 'table_id');
    }
}
