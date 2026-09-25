<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfitDistribution extends Model
{
    use SoftDeletes;
    protected $fillable = ['serial_no', 'year', 'month', 'date', 'product_id', 'invest_qty', 'invest_amount', 'production_qty', 'sales_qty', 'sales_amount', 'profit_amount', 'created_by', 'updated_by', 'deleted_by'];
    protected $appends = ['formattedDate'];

    public function paidList()
    {
        return $this->hasMany(ProfitDistributionList::class, 'profit_distribution_id')->where('paid_amount', '>', 0);
    }

    public function list()
    {
        return $this->hasMany(ProfitDistributionList::class, 'profit_distribution_id');
    }

    public function getFormattedDateAttribute()
    {
        return date('d-m-Y', strtotime($this->date));
    }
}
