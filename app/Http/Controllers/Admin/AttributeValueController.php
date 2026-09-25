<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Models\Attribute;

class AttributeValueController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'attribute-value';
        $this->title = 'Attribute Setup';
        $this->create_title = 'Attribute Value';
        $this->edit_title = 'Attribute Value';
        $this->model = AttributeValue::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $id)
    {
        $data = Attribute::findOrFail($id);
        return HelperClass::resourceDataView($this->model::with(['attribute'])->where('attribute_id', $id)->orderBy('id', 'desc'), null, null, $this->path, $data->name . ' - values');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $id)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {
        $request->validate(['name' => 'required']);

        $this->model::create([
            'attribute_id' => $id,
            'name' => $request->name
        ]);

        return redirect()->route("admin.{$this->path}.index", $id)->withSuccessMessage('Created Successfully!');
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
            return response()->json(['status' => 'success', 'data' => view('admin.attribute-value.partial.edit', ['data' => $data])->render()]);
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
        $data->update(['name' => $request->name]);

        return redirect()->route("admin.{$this->path}.index", $data->attribute_id)->withSuccessMessage('Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return HelperClass::resourceDataDelete($this->model, $id);
    }
}
