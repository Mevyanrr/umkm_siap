<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['name', 'email', 'password', 'role', 'phone'];
    protected $hidden   = ['password'];
    protected $casts    = ['email_verified_at' => 'datetime'];

    public function getJWTIdentifier()   { return $this->getKey(); }
    public function getJWTCustomClaims() { return ['role' => $this->role]; }

    public function products()    { return $this->hasMany(Product::class); }
    public function assessments() { return $this->hasMany(Assessment::class); }
}