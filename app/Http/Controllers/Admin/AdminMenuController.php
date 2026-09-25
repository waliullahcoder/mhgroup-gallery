<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\AdminMenu;
use Illuminate\Http\Request;
use App\Models\AdminMenuAction;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

class AdminMenuController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'admin-menu';
        $this->title = 'Admin Menus';
        $this->create_title = 'Add Menu';
        $this->edit_title = 'Update Menu';
        $this->model = AdminMenu::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addition_btns = [[
            'parameter' => true,
            'target' => '_self',
            'title' => 'View Items',
            'route' => 'admin.admin-menu-action.index',
            'icon' => '<i class="fas fa-eye"></i>',
            'class' => 'btn btn-sm btn-primary mw-fit border-0',
        ]];
        return HelperClass::resourceDataView($this->model::with(['parent'])->orderBy('id', 'desc'), NULL, $addition_btns, $this->path, $this->title, 'children');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $menus = AdminMenu::where('parent_id', $request->root_id)->where('id', '!=', $request->menu_id)->orderBy('order')->get(['id', 'name']);
            return response()->json(['status' => 'success', 'parent_menus' => $menus]);
        }
        $title = $this->create_title;
        $parent_menus = AdminMenu::root()->where('status', 1)->orderBy('order', 'asc')->get();
        return view("admin.{$this->path}.create", compact('title', 'parent_menus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'route' => 'nullable|unique:admin_menus,route',
        ]);

        DB::transaction(function () use ($request) {
            $order = $request->order ?? AdminMenu::where('parent_id', $request->parent_id)
                ->orderByDesc('order')
                ->value('order') + 1;

            $baseName = $request->name;
            $suffix = Permission::where('name', 'like', "$baseName%")->count();
            $permission = Permission::create(['name' => $baseName . ($suffix ? " $suffix" : '')]);

            Role::findByName('Software Admin')->givePermissionTo($permission);

            AdminMenu::create([
                'permission_id' => $permission->id,
                'name'          => $request->name,
                'icon'          => $request->icon,
                'parent_id'     => $request->parent_id,
                'route'         => $request->route,
                'order'         => $order,
                'status'        => $request->status,
                'is_deletable'  => 1,
            ]);
        });

        $this->cacheClear();
        return redirect()->back()->withSuccessMessage('Created Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        if ($request->ajax() && $request->has('root_id')) {
            $parent_menus = AdminMenu::where('parent_id', $request->root_id)->where('id', '!=', $id)->orderBy('order', 'asc')->get(['id', 'name']);
            return response()->json(['status' => 'success', 'parent_menus' => $parent_menus]);
        }

        $menu = AdminMenu::findOrFail($id);
        $root_menus = AdminMenu::root()->with(['children'])->where('id', '!=', $menu->id)->where('status', 1)->orderBy('order', 'asc')->get();
        if ($menu->parent_id) {
            $root_menu_ids = array_column(json_decode($root_menus), 'id');
            if (!in_array($menu->parent_id, $root_menu_ids)) {
                $parent_menus = 1;
                $parent_menu = AdminMenu::where('id', $menu->parent_id)->where('status', 1)->orderBy('order', 'asc')->first();
                $selected_root_menu_id = $root_menus->where('id', $parent_menu->parent_id)->first()->id;
            } else {
                $parent_menus = 0;
                $selected_root_menu_id = '';
            }
        } else {
            $parent_menus = 0;
            $selected_root_menu_id = '';
        }

        $additionalData = [
            'root_menus' => $root_menus,
            'parent_menus' => $parent_menus,
            'selected_root_menu_id' => $selected_root_menu_id
        ];

        return HelperClass::resourceDataEdit($this->model, $id, $this->path, $this->edit_title, $additionalData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string',
            'order' => 'required|integer',
        ]);

        $menu = AdminMenu::findOrFail($id);

        $newName = $request->route ?: $request->name;

        if ($menu->name !== $request->name) {
            $exists = Permission::where('name', $newName)->where('id', '!=', $menu->permission_id)->exists();

            if ($exists) {
                return redirect()->back()->withErrors('Permission name already exists.');
            }

            Permission::where('id', $menu->permission_id)->update(['name' => $newName]);
        }

        DB::transaction(function () use ($request, $menu) {
            $menu->update([
                'parent_id' => $request->parent_id,
                'name'      => $request->name,
                'icon'      => $request->icon,
                'route'     => $request->route,
                'order'     => $request->order,
                'status'    => $request->status,
            ]);

            $role = Role::findByName('Software Admin');
            $permission = Permission::find($menu->permission_id);
            if ($permission) {
                $role->givePermissionTo($permission);
            }
        });

        $this->cacheClear();
        return redirect()->route('admin.admin-menu.index')->withSuccessMessage('Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $menu = AdminMenu::findOrFail($id);

            $actions = AdminMenuAction::where('admin_menu_id', $id)->get();
            foreach ($actions as $action) {
                Permission::findById($action->permission_id)?->delete();
            }

            AdminMenuAction::where('admin_menu_id', $id)->delete();
            Permission::findById($menu->permission_id)?->delete();
            $menu->delete();
        });

        $this->cacheClear();
        return response()->json(['status' => 'success']);
    }

    private function cacheClear(): void
    {
        Artisan::call('optimize:clear');
        Artisan::call('config:cache');
    }
}
