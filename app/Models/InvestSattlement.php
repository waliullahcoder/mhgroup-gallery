<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvestSattlement extends Model
{
    use SoftDeletes;
    protected $fillable = ['investor_id', 'coa_id', 'sattlement_no', 'date', 'invest_qty', 'invest_amount', 'payment', 'remarks', 'created_by', 'updated_by', 'deleted_by'];
    protected $appends = ['formattedDate'];

    public function investor()
    {
        return $this->belongsTo(Investor::class, 'investor_id');
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }

    public function list()
    {
        return $this->hasMany(InvestSattlementList::class, 'invest_sattlement_id');
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class, 'voucher_no', 'sattlement_no')->where('voucher_type', 'Invest Sattlement');
    }

    public function getFormattedDateAttribute()
    {
        return date('d-m-Y', strtotime($this->date));
    }
}
