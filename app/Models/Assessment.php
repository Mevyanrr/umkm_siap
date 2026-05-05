<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Assessment extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'answers', 'product_category',
        'target_country', 'score', 'level', 'ai_prediction',
    ];

    protected $casts = [
        'answers'       => 'array',
        'ai_prediction' => 'array',
        'score'         => 'integer',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
