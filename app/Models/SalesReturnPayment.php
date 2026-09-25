<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturnPayment extends Model
{
    protected $fillable = ['sales_return_id', 'sales_id', 'amount'];

    public function return()
    {
        return $this->belongsTo(SalesReturn::class, 'sales_return_id');
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }
}
