<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOfficer extends Model
{
    use SoftDeletes;
    protected $fillable = ['code', 'name', 'phone', 'email', 'status', 'created_by', 'updated_by', 'deleted_by'];

    public function sales()
    {
        return $this->hasMany(Sales::class, 'sales_officer_id');
    }
}
