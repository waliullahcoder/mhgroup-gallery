<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class RegionController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'region';
        $this->title = 'Region Setup';
        $this->create_title = 'Add Region';
        $this->edit_title = 'Update Region';
        $this->model = Region::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return HelperClass::resourceDataView($this->model::orderBy('id', 'desc'), null, null, $this->path, $this->title, ['areas', 'territories', 'clients']);
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
            'name' => 'required',
            'code' => 'nullable|unique:regions,code',
        ]);

        $this->model::create([
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
            'name' => 'required',
            'code' => 'nullable|unique:regions,code,' . $id,
        ]);

        $data = $this->model::findOrFail($id);
        $data->update([
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
