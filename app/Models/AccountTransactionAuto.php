<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountTransactionAuto extends Model
{
    use SoftDeletes;
    protected $fillable = ['voucher_no', 'voucher_type', 'voucher_date','date', 'coa_id', 'coa_head_code', 'narration', 'debit_amount', 'credit_amount', 'document', 'posted', 'approved', 'approved_by', 'created_by', 'updated_by', 'deleted_by'];
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
