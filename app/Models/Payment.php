<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;
    protected $fillable = ['investor_id', 'coa_id', 'payment_type', 'payment_no', 'date', 'amount', 'remarks', 'created_by', 'updated_by', 'deleted_by'];
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
        return $this->hasMany(PaymentList::class, 'payment_id');
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class, 'voucher_no', 'payment_no')->where('voucher_type', 'Investor Payment');
    }

    public function getFormattedDateAttribute()
    {
        return date('d-m-Y', strtotime($this->date));
    }
}
