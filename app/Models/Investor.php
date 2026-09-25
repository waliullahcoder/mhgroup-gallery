<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Investor extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'coa_id', 'profit_head', 'name', 'image', 'email', 'phone', 'address', 'nid', 'document', 'bkash', 'rocket', 'nagad', 'bank', 'branch', 'account_name', 'account_no', 'profit_percentage', 'status', 'created_by', 'updated_by', 'deleted_by'];

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }

    public function profit_account()
    {
        return $this->belongsTo(Coa::class, 'profit_head');
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransactionAuto::class, 'coa_id', 'coa_id');
    }

    public function invests()
    {
        return $this->hasMany(Invest::class, 'investor_id');
    }
}
