<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Expense extends Model
{
    use SoftDeletes;
    protected $fillable = ['coa_id', 'transaction_no', 'date', 'amount', 'remarks', 'document', 'created_by', 'updated_by', 'deleted_by'];
    protected $appends = ['formattedDate'];

    public function coa()
    {
        return $this->belongsTo(Coa::class);
    }

    public function items()
    {
        return $this->hasMany(ExpenseItem::class);
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class, 'voucher_no', 'transaction_no')->where('voucher_type', 'Expense');
    }

    public function getFormattedDateAttribute()
    {
        return date('d-m-Y', strtotime($this->date));
    }

    protected static function booted()
    {
        // Creating
        static::creating(function ($data) {
            $data->created_by = Auth::id();
            $data->updated_by = Auth::id();
        });

        static::updating(function ($data) {
            $data->updated_by = Auth::id();
        });
    }
}
