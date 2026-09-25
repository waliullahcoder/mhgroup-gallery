<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductEdition extends Model
{
    protected $fillable = ['product_id', 'name', 'status'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productions()
    {
        return $this->hasMany(ProductionList::class, 'product_edition_id');
    }
}
