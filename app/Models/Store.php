<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use SoftDeletes;
    protected $fillable = ['code', 'type', 'name', 'address', 'remarks', 'status', 'created_by', 'updated_by', 'deleted_by'];

    public function productions()
    {
        return $this->hasMany(Production::class, 'store_id');
    }

    public function sales()
    {
        return $this->hasMany(Sales::class, 'store_id');
    }
}
