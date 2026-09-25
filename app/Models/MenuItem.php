<?php

namespace App\Models;

// use App\Traits\TranslatesToBangla;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    // use TranslatesToBangla;
    protected $fillable = ['name', 'menu_id', 'parent_id', 'type', 'link', 'serial'];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id');
    }
}
