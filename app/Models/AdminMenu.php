<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class AdminMenu extends Model
{
    protected $fillable = ['permission_id', 'parent_id', 'name', 'route', 'icon', 'order', 'status', 'is_deletable'];

    protected $casts = [
        'status' => 'boolean',
        'is_deletable' => 'boolean',
    ];

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function children()
    {
        return $this->hasMany(AdminMenu::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(AdminMenu::class, 'parent_id');
    }

    public function actions()
    {
        return $this->hasMany(AdminMenuAction::class, 'admin_menu_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
