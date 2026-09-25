<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitDistributionList extends Model
{
    protected $fillable = ['profit_distribution_id', 'invest_id', 'investor_id', 'product_id', 'profit_per_sale', 'sales_qty', 'invest_qty', 'invest_amount', 'amount', 'paid_amount'];

    public function profitDistribution()
    {
        return $this->belongsTo(ProfitDistribution::class, 'profit_distribution_id');
    }

    public function invest()
    {
        return $this->belongsTo(Invest::class, 'invest_id');
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class, 'investor_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
