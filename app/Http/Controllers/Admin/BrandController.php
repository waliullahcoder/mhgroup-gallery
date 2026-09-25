<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'brand';
        $this->title = 'All Brands';
        $this->create_title = 'Add Brand';
        $this->edit_title = 'Update Brand';
        $this->model = Brand::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return HelperClass::resourceDataView($this->model::orderBy('id', 'desc'), 'image', null, $this->path, $this->title);
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
        $request->validate(['name' => 'required']);

        $this->model::create([
            'name' => $request->name,
            'slug' => HelperClass::generateUniqueSlug($this->model, 'slug', $request->name),
            'image' => $request->hasFile('image') ? HelperClass::saveImage($request->image, 600, $this->path) : null,
            'description' => $request->description,
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
        if ($request->has('edit')) {
            $data = $this->model::findOrFail($id);
            return response()->json(['status' => 'success', 'data' => view('admin.brand.partial.edit', ['data' => $data])->render()]);
        }
        return HelperClass::resourceDataEdit($this->model, $id, $this->path, $this->edit_title);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate(['name' => 'required']);

        $data = $this->model::findOrFail($id);
        $data->update([
            'name' => $request->name,
            'image' => $request->hasFile('image') ? HelperClass::saveImage($request->image, 600, $this->path, $data->image) : $data->image,
            'slug' => HelperClass::generateUniqueSlug($this->model, 'slug', $request->name, $id),
            'description' => $request->description,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return HelperClass::resourceDataDelete($this->model, $id, 'image');
    }
}
