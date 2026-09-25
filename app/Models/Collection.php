<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use SoftDeletes;
    protected $fillable = ['client_id', 'coa_id', 'sales_id', 'sales_return_id', 'payment_no', 'date', 'payment_type', 'collection_type', 'amount', 'remarks', 'created_by', 'updated_by', 'deleted_by'];
    protected $appends = ['formattedDate'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    public function return()
    {
        return $this->belongsTo(SalesReturn::class, 'sales_return_id');
    }

    public function list()
    {
        return $this->hasMany(CollectionList::class, 'collection_id');
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class, 'voucher_no', 'payment_no')->where('voucher_type', 'Client Collection');
    }

    public function getFormattedDateAttribute()
    {
        return date('d-m-Y', strtotime($this->date));
    }
}
