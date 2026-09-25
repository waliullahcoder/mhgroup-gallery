<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sales extends Model
{
    use SoftDeletes;
    protected $fillable = ['client_id', 'store_id', 'sales_officer_id', 'coa_id', 'sale_type', 'invoice', 'date', 'amount', 'discount', 'tax', 'tax_amount', 'net_amount', 'paid', 'return_amount', 'return_paid', 'remarks', 'created_by', 'updated_by', 'deleted_by'];
    protected $appends = ['formattedDate'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function tso()
    {
        return $this->belongsTo(SalesOfficer::class, 'sales_officer_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }

    public function list()
    {
        return $this->hasMany(SalesList::class, 'sales_id');
    }

    public function collections()
    {
        return $this->hasMany(CollectionList::class, 'sales_id');
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class, 'voucher_no', 'invoice')->where('voucher_type', 'Client Sales');
    }

    public function returns()
    {
        return $this->hasMany(SalesReturnList::class, 'sales_id');
    }

    public function getFormattedDateAttribute()
    {
        return date('d-m-Y', strtotime($this->date));
    }
}
