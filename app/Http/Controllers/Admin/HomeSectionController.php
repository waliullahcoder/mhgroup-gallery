<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\HomeSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeSectionController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'home-section';
        $this->title = 'Homepage Sections';
        $this->create_title = 'Add Section';
        $this->edit_title = 'Update Section';
        $this->model = HomeSection::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            foreach ($request->order as $index => $item) {
                $this->model::where('id', $item['id'])->update(['serial' => $index + 1]);
            }

            return response()->json(['status' => 'success']);
        }

        $title = $this->title;
        $homeSections = $this->model::orderBy('serial', 'asc')->get();
        return view("admin.{$this->path}.index", compact('title', 'homeSections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $type = $request->type;
            return response()->json([
                'status' => 'success',
                'data'  => view('admin.home-section.partial.create', compact('type'))->render()
            ]);
        }

        $title = $this->create_title;
        return view("admin.{$this->path}.create", compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required'
        ]);

        if ($request->type == 'Category Product') {
            $request->validate([
                'category_id' => 'required',
            ]);
        }

        try {
            DB::transaction(function () use ($request) {
                $data = $this->model::create([
                    'title' => $request->title,
                    'type' => $request->type,
                    'category_id' => $request->category_id,
                    'product_type' => json_encode($request->product_type ?? []),
                    'serial' => $this->model::max('serial') + 1,
                    'created_by' => Auth::id(),
                ]);

                if (in_array($request->type, ['Featured Category', 'Category Carousel'])) {
                    $request->validate([
                        'category_ids' => 'required',
                    ]);
                }

                if ($request->type == 'Popular Writter') {
                    $request->validate([
                        'author_ids' => 'required',
                    ]);
                }

                if (in_array($request->type, ['Trending Product', 'New Product'])) {
                    $request->validate([
                        'product_type' => 'required',
                    ]);
                }
            });

            return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Created Successfully!');
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
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
        return HelperClass::resourceDataDelete($this->model, $id);
    }
}
