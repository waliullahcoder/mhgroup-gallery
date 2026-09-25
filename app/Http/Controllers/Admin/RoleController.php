<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\AdminMenu;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'role';
        $this->title = 'Role Setup';
        $this->create_title = 'Add Role';
        $this->edit_title = 'Update Role';
        $this->model = Role::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->model::whereNotIn('name', ['Software Admin'])->orderBy('id', 'desc');
        if (!Auth::user()->hasRole('Software Admin')) {
            $data->whereNotIn('name', ['System Admin']);
        }
        $addition_btns = [[
            'parameter' => true,
            'target' => '_self',
            'title' => 'Permissions',
            'route' => 'admin.role-permission.edit',
            'icon' => '<i class="far fa-lock-alt"></i>',
            'class' => 'btn btn-sm btn-success border-0',
        ]];
        return HelperClass::resourceDataView($data, null, $addition_btns, $this->path, $this->title);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = $this->create_title;
        return view("admin.{$this->path}.create", compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'unique:roles,name']
        ]);
        $this->model::create(['name' => $request->name]);
        return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return HelperClass::resourceDataEdit($this->model, $id, $this->path, $this->edit_title);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'unique:roles,name,' . $id]
        ]);
        $data = $this->model::findById($id);
        $data->update([
            'name' => $request->name
        ]);
        return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        // Delete Multiple Items
        if ($request->id) {
            foreach ($request->id as $id) {
                $role = $this->model::findById($id);
                $role->syncPermissions([]);
                $role->delete();
            }
            return response()->json(['status' => 'success']);
        }

        // Delete Single Item
        $role = $this->model::findById($id);
        $role->syncPermissions([]);
        $role->delete();
        return response()->json(['status' => 'success']);
    }

    public function rolePermissionEdit($id)
    {
        $role = $this->model::findById($id);
        $decoded_permission_id = json_decode($role->permissions);
        $permission_id = array_column($decoded_permission_id, 'id');
        $root_menus = AdminMenu::root()->with(['children', 'actions', 'permission'])->where('status', 1)->orderBy('order', 'asc')->get();
        return view("admin.{$this->path}.role_permission", compact('role', 'permission_id', 'root_menus'));
    }

    public function rolePermissionUpdate(Request $request, $id)
    {
        $role = $this->model::findById($id);
        $role->permissions()->detach();
        $permission_ids = $request->permission_id;

        if (!empty($permission_ids)) {
            $permissions = Permission::whereIn('id', $permission_ids)->get();
            $role->syncPermissions($permissions);
        }
        return redirect()->back()->withSuccessMessage('Updated Successfully!');
    }
}
