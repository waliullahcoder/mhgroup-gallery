<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    protected $fillable = ['attribute_id', 'name', 'status', 'created_by', 'updated_by'];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    // Variants that have this value
    public function variants()
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_values')
            ->withPivot('attribute_id')
            ->withTimestamps();
    }
}
