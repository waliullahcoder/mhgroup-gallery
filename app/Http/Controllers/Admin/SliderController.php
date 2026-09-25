<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\Slider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SliderController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'slider';
        $this->title = 'Slider Setup';
        $this->create_title = 'Add Slider';
        $this->edit_title = 'Update Slider';
        $this->model = Slider::class;
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
        $title = $this->create_title;
        return view("admin.{$this->path}.create", compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image',
            'mobile_image' => 'required|image'
        ]);

        $this->model::create([
            'image' => $request->hasFile('image') ? HelperClass::saveImage($request->image, 1400, $this->path) : null,
            'mobile_image' => $request->hasFile('mobile_image') ? HelperClass::saveImage($request->mobile_image, 800, $this->path) : null,
            'link' => $request->link,
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
            'image' => 'nullable|image',
            'mobile_image' => 'nullable|image'
        ]);

        $data = $this->model::findOrFail($id);
        $data->update([
            'image' => $request->hasFile('image') ? HelperClass::saveImage($request->image, 1400, $this->path, $data->image) : $data->image,
            'mobile_image' => $request->hasFile('mobile_image') ? HelperClass::saveImage($request->mobile_image, 1400, $this->path, $data->mobile_image) : $data->mobile_image,
            'link' => $request->link,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return HelperClass::resourceDataDelete($this->model, $id, ['image', 'mobile_image']);
    }
}
