<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invest extends Model
{
    use SoftDeletes;
    protected $fillable = ['investor_id', 'product_id', 'invest_no', 'date', 'qty', 'amount', 'deposit_type', 'bkash', 'rocket', 'nagad', 'bank_account', 'remarks', 'approved', 'sattled', 'coa_id', 'created_by', 'updated_by', 'deleted_by'];
    protected $appends = ['formattedDate'];

    public function investor()
    {
        return $this->belongsTo(Investor::class, 'investor_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }

    public function payments()
    {
        return $this->hasMany(PaymentList::class, 'invest_id');
    }

    public function sattlements()
    {
        return $this->hasMany(InvestSattlementList::class, 'invest_id');
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class, 'voucher_no', 'invest_no')->where('voucher_type', 'Invest');
    }

    public function getFormattedDateAttribute()
    {
        return date('d-m-Y', strtotime($this->date));
    }
}
