<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $menu = Menu::findOrFail($id);
        return view('admin.menu-item.index', compact('menu'));
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
        $menuItemOrder = json_decode($request->input('data'));
        $this->orderMenu($menuItemOrder, null);
        $this->clearCache();
        return response()->json(['status' => 'success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        //
    }

    private function orderMenu(array $menuItems, $parentId)
    {
        foreach ($menuItems as $index => $menuItem) {
            $item = MenuItem::findOrFail($menuItem->id);
            $item->update([
                'serial' => $index + 1,
                'parent_id' => $parentId
            ]);

            if (isset($menuItem->children)) {
                $this->orderMenu($menuItem->children, $item->id);
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $serial = MenuItem::where('menu_id', $id)->max('serial') + 1;
        MenuItem::create([
            'name'      => $request->name,
            'menu_id'   => $id,
            'type'      => $request->type,
            'link'      => $request->link,
            'serial'    => $serial,
        ]);

        $this->clearCache();
        return redirect()->back()->withSuccessMessage('Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = MenuItem::findOrFail($id);
        $item->delete();
        $this->clearCache();
        return response()->json(['status' => 'success', 'message' => 'Deleted Successfully!']);
    }

    private function clearCache(): void
    {
        Artisan::call('optimize:clear');
        Artisan::call('config:cache');
    }
}
