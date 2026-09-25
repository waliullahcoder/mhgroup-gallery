<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

class MenuController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'menu';
        $this->title = 'All Menu';
        $this->create_title = 'Create Menu';
        $this->edit_title = 'Edit Menu';
        $this->model = Menu::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $additionalBtns = [[
            'parameter' => true,
            'target' => '_self',
            'title' => 'View Items',
            'route' => 'admin.menu-item.index',
            'icon' => '<i class="fas fa-eye"></i>',
            'class' => 'btn btn-sm btn-primary',
        ]];
        return HelperClass::resourceDataView($this->model::orderBy('id', 'desc'), null, $additionalBtns, $this->path, $this->title);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id'  => 'required',
            'name'   => 'required|unique:menus,name',
            'position'  => 'required',
            'status'    => 'required|boolean',
        ]);

        // Restrict header and footer menu creation
        $existingMenus = Menu::where('position', $request->position)->count();

        if ($request->position === 'header' && $existingMenus >= 10) {
            return back()->withErrors('Header menu already exists!');
        }

        if ($request->position === 'footer' && $existingMenus >= 10) {
            return back()->withErrors('Maximum number of footer menus added!');
        }

        $this->model::create($request->only(['name', 'position', 'status','url','category_id']));
        $this->clearCache();

        return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Menu created successfully!');
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
        if (request()->ajax() && request('edit')) {
            $data = $this->model::findOrFail($id);
            $form_link = route('admin.menu.update', $id);
            return response()->json(['status' => 'success', 'data' => $data, 'form_link' => $form_link]);
        }

        $title = $this->edit_title;
        return HelperClass::resourceDataEdit($this->model, $id, $this->path, $title, null);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'category_id'  => 'required',
            'name' => 'required|unique:menus,name,' . $id,
            'position' => 'required',
            'status' => 'required|boolean',
        ]);

        $menu = $this->model::findOrFail($id);

        // Count existing menus for the requested position (excluding the current one)
        $existingMenus = $this->model::where('position', $request->position)->where('id', '!=', $id)->count();

        // Restrict header and footer menu limits
        if ($request->position === 'header' && $existingMenus >= 10) {
            return back()->withErrors('Header menu already exists!');
        }

        if ($request->position === 'footer' && $existingMenus >= 10) {
            return back()->withErrors('Maximum number of footer menus added!');
        }

        $menu->update([
            'category_id' => $request->category_id,
            'name'   => $request->name,
            'position'  => $request->position,
            'status'    => $request->status,
            'url'    => $request->url,
        ]);

        $this->clearCache();
        return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Menu updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Recovery Deleted Data
        if (request()->has('recovery') && request('recovery') == 'true') {
            $data = $this->model::onlyTrashed()->findOrFail($id);
            $data->restore();
            $this->clearCache();
            return response()->json(['status' => 'success', 'message' => 'Recovered Successfully!']);
        }

        // Delete Multiple Items Permanent
        if (request()->has('id') && request()->has('parmanent') && request('parmanent') == 'true') {
            foreach (request('id') as $id) {
                $data = $this->model::onlyTrashed()->findOrFail($id);
                $data->forceDelete();
            }
            $this->clearCache();
            return response()->json(['status' => 'success', 'message' => 'Permanently Deleted!']);
        }

        // Delete Single Item Permanent
        if (request()->has('parmanent') && request('parmanent') == 'true') {
            $data = $this->model::onlyTrashed()->findOrFail($id);
            $data->forceDelete();
            $this->clearCache();
            return response()->json(['status' => 'success', 'message' => 'Permanently Deleted!']);
        }

        // Delete Multiple Items
        if (request()->has('id')) {
            foreach (request('id') as $id) {
                $data = $this->model::findOrFail($id);
                $data->update(['deleted_by' => Auth::user()->id]);
                $data->delete();
            }
            $this->clearCache();
            return response()->json(['status' => 'success', 'message' => 'Successfully Deleted!']);
        }

        // Delete Single Item
        $data = $this->model::findOrFail($id);
        $data->update(['deleted_by' => Auth::user()->id]);
        $data->delete();
        $this->clearCache();

        return response()->json(['status' => 'success', 'message' => 'Successfully Deleted!']);
    }

    private function clearCache(): void
    {
        Artisan::call('optimize:clear');
        Artisan::call('config:cache');
    }
}
