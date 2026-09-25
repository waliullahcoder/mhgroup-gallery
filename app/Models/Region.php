<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use SoftDeletes;
    protected $fillable = ['code', 'name', 'incharge', 'phone', 'email', 'address', 'status', 'created_by', 'updated_by', 'deleted_by'];

    public function areas()
    {
        return $this->hasMany(Area::class, 'region_id');
    }

    public function territories()
    {
        return $this->hasMany(Territory::class, 'region_id');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'region_id');
    }
}
