<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends Authenticatable implements JWTSubject
{
    protected $table = 'customers';

    protected $primaryKey = 'customer_id';

    public $timestamps = false;

    protected $fillable = [
        'full_name',
        'username',
        'password_hash',
        'token_version',
        'phone_number',
        'points',
        'membership_level',
        'email',
        'loyalty_points',
        'created_at',
    ];

    protected $casts = [
        'token_version' => 'integer',
        'points' => 'integer',
        'loyalty_points' => 'integer',
        'created_at' => 'datetime',
    ];

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id', 'customer_id');
    }
}
