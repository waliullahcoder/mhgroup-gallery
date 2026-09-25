<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturnList extends Model
{
    protected $fillable = ['sales_return_id', 'client_id', 'store_id', 'sales_id', 'sales_list_id', 'product_id', 'product_edition_id', 'rate', 'qty', 'amount'];

    public function return()
    {
        return $this->belongsTo(SalesReturn::class, 'sales_return_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    public function salesList()
    {
        return $this->belongsTo(SalesList::class, 'sales_list_id');
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
