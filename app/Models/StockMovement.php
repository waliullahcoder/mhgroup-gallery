<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'store_id',
        'product_id',
        'product_variant_id',
        'quantity',      // positive = in, negative = out
        'type',          // purchase_receipt, sales, transfer_in, transfer_out, adjustment
        'reference_id',  // optional related record id
        'reference_type' // optional related record type
    ];

    /**
     * The store where this movement happened
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * The product involved in this movement
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * The product variant involved in this movement
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Polymorphic reference to related record
     */
    public function reference()
    {
        return $this->morphTo();
    }
}
