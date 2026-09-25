<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
    protected $fillable = ['account_transaction_auto_id', 'voucher_no', 'voucher_date', 'voucher_type', 'date', 'coa_id', 'coa_head_code', 'narration', 'debit_amount', 'credit_amount', 'document', 'posted', 'approved', 'approved_by', 'created_by', 'updated_by', 'deleted_by'];
    protected $appends = ['formattedDate'];

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }

    public function getFormattedDateAttribute()
    {
        return date('d-m-Y', strtotime($this->date));
    }
}
