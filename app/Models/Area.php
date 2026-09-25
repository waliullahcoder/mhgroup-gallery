<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SoftDeletes;
    protected $fillable = ['region_id', 'code', 'name', 'incharge', 'phone', 'email', 'address', 'status', 'created_by', 'updated_by', 'deleted_by'];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function territories()
    {
        return $this->hasMany(Territory::class, 'area_id');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'region_id');
    }
}
