<?php

namespace App\Models;

// use App\Traits\TranslatesToBangla;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'position', 'status','url','category_id', 'created_by', 'updated_by', 'deleted_by'];

    public function rootItems()
    {
        return $this->hasMany(MenuItem::class, 'menu_id')->whereNull('parent_id')->orderBy('serial', 'asc')->with(['children']);
    }

    public function items()
    {
        return $this->hasMany(MenuItem::class, 'menu_id')->orderBy('serial', 'asc');
    }

    protected static function booted()
    {
        // Automatically set created_by and updated_by
        static::creating(function ($data) {
            $data->created_by = Auth::id();
            $data->updated_by = Auth::id();
        });

        static::updating(function ($data) {
            $data->updated_by = Auth::id();
        });
    }
}
