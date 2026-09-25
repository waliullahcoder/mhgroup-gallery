<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttributeController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'attribute';
        $this->title = 'Attribute Setup';
        $this->create_title = 'Add Attribute';
        $this->edit_title = 'Update Attribute';
        $this->model = Attribute::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addition_btns = [[
            'route' => 'admin.attribute-value.index',
            'parameter' => true,
            'target' => '_self',
            'class' => 'btn btn-sm btn-primary tt mw-fit',
            'title' => 'Values',
            'icon' => '<i class="fas fa-eye"></i>',
        ]];

        return HelperClass::resourceDataView($this->model::orderBy('id', 'desc'), NULL, $addition_btns, $this->path, $this->title, 'values');
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
        $request->validate(['name' => 'required']);

        $this->model::create([
            'name' => $request->name,
            'slug' => HelperClass::generateUniqueSlug($this->model, 'slug', $request->name),
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
            return response()->json(['status' => 'success', 'data' => view('admin.attribute.partial.edit', ['data' => $data])->render()]);
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
        return HelperClass::resourceDataDelete($this->model, $id);
    }
}
