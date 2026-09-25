<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;
    protected $fillable = ['coa_id', 'user_id','region_id', 'area_id', 'territory_id', 'code', 'name', 'contact_person', 'phone', 'email', 'address', 'credit_limit', 'bin_no', 'status', 'created_by', 'updated_by', 'deleted_by'];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function territory()
    {
        return $this->belongsTo(Territory::class, 'territory_id');
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }

    public function sales()
    {
        return $this->hasMany(Sales::class, 'client_id');
    }

    public function collections()
    {
        return $this->hasMany(Collection::class, 'client_id');
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransactionAuto::class, 'coa_id', 'coa_id');
    }
    public function user()
    {
        return $this->hasMany(User::class, 'user_id');
    }
}
