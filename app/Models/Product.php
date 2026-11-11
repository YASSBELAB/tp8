<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'category_id',
    ];

    protected $casts = [
        'price' => 'float',
    ];

    // A product belongs to one category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
