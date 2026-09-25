<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'variant', 'sku', 'purchase_price', 'regular_price', 'sale_price', 'discount', 'discount_type', 'image', 'stock', 'status'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function values()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_values');
    }

    // Shortcut: directly get attributes (through pivot)
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_variant_values')
            ->withPivot('attribute_value_id')
            ->withTimestamps();
    }

    // Shortcut: directly get attribute values (through pivot)
    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_values')
            ->withPivot('attribute_id')
            ->withTimestamps();
    }
  
}
