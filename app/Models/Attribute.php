<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'slug', 'description', 'status', 'created_by', 'updated_by', 'deleted_by'];

    public function values()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id');
    }

    // Variants that use this attribute
    public function variants()
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_values')
            ->withPivot('attribute_value_id')
            ->withTimestamps();
    }
}
