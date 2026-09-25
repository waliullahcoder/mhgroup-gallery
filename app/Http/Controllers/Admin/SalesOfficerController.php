<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use Illuminate\Http\Request;
use App\Models\SalesOfficer;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SalesOfficerController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'sales-officer';
        $this->title = 'Sales Officers';
        $this->create_title = 'Add Sales Officer';
        $this->edit_title = 'Update Sales Officer';
        $this->model = SalesOfficer::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return HelperClass::resourceDataView($this->model::orderBy('id', 'desc'), null, null, $this->path, $this->title);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
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
            'code' => 'nullable|unique:sales_officers,code'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $this->model::create([
                    'code' => $request->code,
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'created_by' => Auth::id(),
                ]);
            });
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
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
            'code' => 'nullable|unique:sales_officers,code'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $data = $this->model::findOrFail($id);
                $data->update([
                    'code' => $request->code,
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'updated_by' => Auth::id(),
                ]);
            });
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }

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
