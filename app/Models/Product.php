<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Product extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'name', 'category', 'description',
        'price_usd', 'production_capacity', 'target_countries',
        'certifications', 'images', 'status',
    ];

    protected $casts = [
        'target_countries' => 'array',
        'certifications'   => 'array',
        'images'           => 'array',
        'price_usd'        => 'float',
    ];

    public function seller() { return $this->belongsTo(User::class, 'user_id'); }
}
