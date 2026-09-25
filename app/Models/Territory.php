<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Territory extends Model
{
    use SoftDeletes;
    protected $fillable = ['region_id', 'area_id', 'code', 'name', 'incharge', 'phone', 'email', 'address', 'status', 'created_by', 'updated_by', 'deleted_by'];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'region_id');
    }
}
