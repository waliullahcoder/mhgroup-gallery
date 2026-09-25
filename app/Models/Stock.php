<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'store_id',
        'product_id',
        'product_variant_id',
        'quantity',
    ];

    /**
     * The store (warehouse) this stock belongs to
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * The product this stock belongs to
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * The product variant this stock belongs to
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * All stock movements for this stock
     */
    public function movements()
    {
        return $this->hasMany(StockMovement::class)
            ->where('store_id', $this->store_id)
            ->where('product_id', $this->product_id)
            ->where('product_variant_id', $this->product_variant_id);
    }
}
