<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionList extends Model
{
    protected $fillable = ['production_id', 'store_id', 'product_id', 'product_edition_id', 'qty'];

    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function edition()
    {
        return $this->belongsTo(ProductEdition::class, 'product_edition_id');
    }
}
