<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasUuids;

    protected $fillable = ['name', 'email', 'password', 'role', 'phone'];
    protected $hidden   = ['password'];
    protected $casts    = ['email_verified_at' => 'datetime'];

    public function getJWTIdentifier()   { return $this->getKey(); }
    public function getJWTCustomClaims() { return ['role' => $this->role]; }

    public function products()    { return $this->hasMany(Product::class); }
    public function assessments() { return $this->hasMany(Assessment::class); }
}
