<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\Area;
use App\Models\Region;
use App\Models\Territory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class TerritoryController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'territory';
        $this->title = 'Territory Setup';
        $this->create_title = 'Add Territory';
        $this->edit_title = 'Update Territory';
        $this->model = Territory::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return HelperClass::resourceDataView($this->model::with(['region', 'area'])->orderBy('id', 'desc'), null, null, $this->path, $this->title, 'clients');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $areas = Area::where('region_id', $request->region_id)->where('status', true)->orderBy('name', 'asc')->get();
            return response()->json(['status' => 'success', 'areas' => $areas]);
        }

        $title = $this->create_title;
        $regions = Region::where('status', true)->orderBy('name', 'asc')->get();
        return view("admin.{$this->path}.create", compact('title', 'regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'region_id' => 'required',
            'area_id' => 'required',
            'name' => 'required',
            'code' => 'nullable|unique:territories,code',
        ]);

        $this->model::create([
            'company_id' => Auth::user()->company_id ?? 1,
            'region_id' => $request->region_id,
            'area_id' => $request->area_id,
            'code' => $request->code,
            'name' => $request->name,
            'incharge' => $request->incharge,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Created Successfully!');
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
        if ($request->ajax()) {
            $areas = Area::where('region_id', $request->region_id)->where('status', true)->orderBy('name', 'asc')->get();
            return response()->json(['status' => 'success', 'areas' => $areas]);
        }

        $data = $this->model::findOrFail($id);
        $additionalData = [
            'regions' => Region::where('status', true)->orWhere('id', $data->region_id)->orderBy('name', 'asc')->get()
        ];
        return HelperClass::resourceDataEdit($this->model, $id, $this->path, $this->edit_title, $additionalData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'region_id' => 'required',
            'area_id' => 'required',
            'name' => 'required',
            'code' => 'nullable|unique:territories,code,' . $id,
        ]);

        $data = $this->model::findOrFail($id);
        $data->update([
            'region_id' => $request->region_id,
            'area_id' => $request->area_id,
            'code' => $request->code,
            'name' => $request->name,
            'incharge' => $request->incharge,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return HelperClass::resourceDataDelete($this->model, $id);
    }
}
