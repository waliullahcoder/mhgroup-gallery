<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use Illuminate\Http\Request;
use App\Models\AdminMenuAction;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

class AdminMenuActionController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'admin-menu-action';
        $this->title = 'Admin Menu Actions';
        $this->create_title = 'Add menu action';
        $this->edit_title = 'Update menu action';
        $this->model = AdminMenuAction::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        return HelperClass::resourceDataView($this->model::with(['menu'])->where('admin_menu_id', $id)->orderBy('id', 'desc'), null, null, $this->path, $this->title);
    }

    private function clearCache(): void
    {
        Artisan::call('optimize:clear');
        Artisan::call('config:cache');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $title = $this->create_title;
        return view("admin.{$this->path}.create", compact('title', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required',
            'route' => 'required',
            'unique:admin_menu_actions,route'
        ]);

        $check_permission = Permission::where('name', $request->route)->first();
        if ($check_permission) {
            return redirect()->back()->withErrors('Action Already Added');
        }

        DB::transaction(function () use ($request, $id) {
            $permission = Permission::create(['name' => $request->route]);
            $role = Role::findByName('Software Admin');
            $role->givePermissionTo($permission);

            AdminMenuAction::create([
                'permission_id' => $permission->id,
                'admin_menu_id' => $id,
                'name' => $request->name,
                'route' => $request->route,
                'status' => $request->status,
            ]);
        });

        $this->clearCache();
        return redirect()->back()->withSuccessMessage('Created Succcessfully!');
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
            'name'  => 'required',
            'route' => ['required', 'unique:admin_menu_actions,route,' . $id]
        ]);

        $action = AdminMenuAction::findOrFail($id);
        if ($action->route !== $request->route) {
            $check_permission = Permission::where('name', $request->route)->first();
            if ($check_permission) {
                return redirect()->back()->withErrors('Action Already Added');
            }
        }

        Permission::where('id', $action->permission_id)->update(['name' => $request->route]);

        $action->update([
            'name' => $request->name,
            'route' => $request->route,
            'status' => $request->status,
        ]);

        $this->clearCache();
        return redirect()->route('admin.admin-menu-action.index', $action->admin_menu_id)->withSuccessMessage('Updated Succcessfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $action = AdminMenuAction::findOrFail($id);
        Permission::findById($action->permission_id)->delete();
        $action->delete();

        $this->clearCache();
        return response()->json(['status' => 'success']);
    }
}
