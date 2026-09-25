<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentList extends Model
{
    protected $fillable = ['payment_id', 'distribution_list_id', 'invest_id', 'investor_id', 'amount'];

    public function profitDistribution()
    {
        return $this->belongsTo(ProfitDistributionList::class, 'distribution_list_id');
    }
}
