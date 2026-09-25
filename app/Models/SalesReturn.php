<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesReturn extends Model
{
    use SoftDeletes;
    protected $fillable = ['client_id', 'store_id', 'return_no', 'date', 'amount', 'remarks', 'created_by', 'updated_by', 'deleted_by'];
    protected $appends = ['formattedDate'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function list()
    {
        return $this->hasMany(SalesReturnList::class, 'sales_return_id');
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class, 'voucher_no', 'return_no')->where('voucher_type', 'Sales Return');
    }

    public function collections()
    {
        return $this->hasMany(SalesReturnPayment::class, 'sales_return_id');
    }

    public function getFormattedDateAttribute()
    {
        return date('d-m-Y', strtotime($this->date));
    }
}
