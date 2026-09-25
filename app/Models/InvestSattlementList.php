<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestSattlementList extends Model
{
    protected $fillable = ['invest_sattlement_id', 'investor_id', 'invest_id', 'product_id', 'invest_qty', 'invest_amount', 'payment'];

    public function sattlement()
    {
        return $this->belongsTo(InvestSattlement::class, 'invest_sattlement_id');
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class, 'investor_id');
    }

    public function invest()
    {
        return $this->belongsTo(Invest::class, 'invest_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
