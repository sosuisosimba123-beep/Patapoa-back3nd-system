<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'brand',
        'unit',
        'description',
        'primary_image_url',
        'backup_image_url',
        'slug',
        'search_tags',
    ];

    protected $casts = [
        'search_tags' => 'json',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Product::class, 'master_product_id');
    }
}
