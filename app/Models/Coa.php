<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coa extends Model
{
    use SoftDeletes;
    protected $fillable = ['parent_id', 'head_code', 'head_name', 'transaction', 'general', 'head_type', 'status', 'updateable', 'created_by', 'updated_by', 'deleted_by'];

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function children()
    {
        return $this->hasMany(Coa::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Coa::class, 'parent_id');
    }

    public function allChildrenRecursive()
    {
        return $this->children()->with('allChildrenRecursive');
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransactionAuto::class, 'coa_id');
    }
}
