<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseItem extends Model
{
    protected $fillable = ['expense_id', 'coa_id', 'amount', 'is_distributed'];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class);
    }
}
