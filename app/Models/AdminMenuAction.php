<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class AdminMenuAction extends Model
{
    protected $fillable = ['permission_id', 'admin_menu_id', 'name', 'route', 'status',];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function menu()
    {
        return $this->belongsTo(AdminMenu::class, 'admin_menu_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
