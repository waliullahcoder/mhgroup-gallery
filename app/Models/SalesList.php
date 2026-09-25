<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesList extends Model
{
    protected $fillable = ['sales_id', 'store_id', 'client_id', 'product_id', 'product_edition_id', 'price', 'commission', 'commission_amount', 'rate', 'qty', 'amount', 'discount', 'net_amount', 'return_qty', 'return_amount', 'distributed'];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
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
