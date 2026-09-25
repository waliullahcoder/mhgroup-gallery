<?php

namespace App\Http\Controllers\Admin;

use App\Models\Coa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CoaController extends Controller
{
    public $path;
    public $title;
    public $model;
    public $hasGL = 0;
    public function __construct()
    {
        $this->path = 'coa';
        $this->title = 'Chart of Accounts';
        $this->model = Coa::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tree = Coa::with('allChildrenRecursive')->whereNull('parent_id')->get();
        $coa = self::menuUi($tree);
        $html = '<li class="item"><span>COA</span><ul>' . $coa . '</ul></li>';

        $title = 'Chart of Accounts';
        return view("admin.{$this->path}.index", compact('title', 'html'));
    }

    public static function menuUi($coaLists)
    {
        $html = '';
        foreach ($coaLists as $element) {
            $html .= '<li><a href="javascript:" onclick="loadData(' . $element->id . ',' . ($element->updateable && count($element->transactions) == 0 ? 'true' : 'false') . ')">' . $element->head_name . '</a>';

            if ($element->allChildrenRecursive->isNotEmpty()) {
                $html .= '<ul>' . self::subMenu($element->allChildrenRecursive) . '</ul>';
            }

            $html .= '</li>';
        }
        return $html;
    }

    public static function subMenu($children)
    {
        $html = '';
        foreach ($children as $element) {
            $html .= '<li><a href="javascript:" onclick="loadData(' . $element->id . ',' . ($element->updateable && count($element->transactions) == 0 ? 'true' : 'false') . ')">' . $element->head_name . '</a>';

            if ($element->allChildrenRecursive->isNotEmpty()) {
                $html .= '<ul>' . self::subMenu($element->allChildrenRecursive) . '</ul>';
            }

            $html .= '</li>';
        }
        return $html;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax() && $request->has('parent_id')) {
            $parent = $this->model::findOrFail($request->parent_id);
            $baseCode = $parent->head_code;
            $newCode = $baseCode . '01';

            $maxSiblingCode = $this->model::where('parent_id', $request->parent_id)->max('head_code');
            if ($maxSiblingCode) {
                $nextNumber = (int)substr($maxSiblingCode, strlen($baseCode)) + 1;
                $nextNumber = str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
                $newCode = $baseCode . $nextNumber;
            }

            return response()->json([
                'status' => 'success',
                'parent_head' => $parent->head_name,
                'head_code' => $newCode,
            ]);
        }

        if ($request->ajax()) {
            $data = $this->model::with('allChildrenRecursive')->findOrFail($request->id);

            $this->hasGL = 0;
            if ($data->allChildrenRecursive->isNotEmpty()) {
                $this->checkGL($data->allChildrenRecursive);
            }

            return response()->json([
                'status' => 'success',
                'data' => $data,
                'form_link' => route('admin.coa.update', $request->id),
                'parent_head_name' => optional($data->parent)->head_name,
                'children' => $data->allChildrenRecursive->count(),
                'parent_head_general' => optional($data->parent)->general,
                'hasGL' => $this->hasGL,
            ]);
        }
    }

    private function checkGL($children)
    {
        foreach ($children as $item) {
            if ($item->general == 1) {
                $this->hasGL = 1;
                return;
            }

            if ($item->general == 0 && $item->allChildrenRecursive->isNotEmpty()) {
                $this->checkGL($item->allChildrenRecursive);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'head_code' => 'required|string|unique:coas,head_code',
            'head_name' => 'required|string|max:255',
            'head_type' => 'required|string',
        ]);

        $this->model::create([
            'parent_id'   => $request->parent_id,
            'head_code'   => $request->head_code,
            'head_name'   => $request->head_name,
            'transaction' => $request->boolean('transaction'),
            'general'     => $request->boolean('general'),
            'head_type'   => $request->head_type,
            'status'      => $request->boolean('status'),
            'created_by'  => Auth::id()
        ]);

        return redirect()->back()->withSuccessMessage('Created Successfully!');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $this->model::findOrFail($id);

        $request->validate([
            'head_code' => 'required|string|unique:coas,head_code,' . $id,
            'head_name' => 'required|string|max:255',
            'head_type' => 'required|string',
        ]);

        $data->update([
            'head_code'   => $request->head_code,
            'head_name'   => $request->head_name,
            'transaction' => $request->boolean('transaction'),
            'general'     => $request->boolean('general'),
            'head_type'   => $request->head_type,
            'status'      => $request->boolean('status'),
            'updated_by'  => Auth::id()
        ]);

        return redirect()->back()->withSuccessMessage('Updated Successfully!');
    }
}
