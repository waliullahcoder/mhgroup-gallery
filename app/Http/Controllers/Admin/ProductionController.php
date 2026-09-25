<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\Store;
use App\Models\Product;
use App\Models\Production;
use Illuminate\Http\Request;
use App\Models\ProductEdition;
use App\Models\ProductVariant;
use App\Models\ProductionList;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ProductionController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'production';
        $this->title = 'Productions';
        $this->create_title = 'Add Production';
        $this->edit_title = 'Update Production';
        $this->model = Production::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addition_btns = [
            [
                'parameter' => true,
                'target' => '_self',
                'title' => 'View Voucher',
                'route' => "admin.{$this->path}.show",
                'icon' => '<i class="fas fa-eye"></i>',
                'class' => 'btn btn-sm btn-print-1 mw-fit border-0',
            ],
            [
                'parameter' => true,
                'target' => '_self',
                'title' => 'Print Voucher',
                'route' => "admin.{$this->path}.print",
                'icon' => '<i class="fal fa-print"></i>',
                'class' => 'btn btn-sm btn-print-2 mw-fit border-0',
            ]
        ];

        return HelperClass::resourceDataView($this->model::with(['store'])->orderBy('id', 'desc'), null, $addition_btns, $this->path, $this->title);
    }

    /**
     * Generate Purchase No.
     */
    public function productionNo()
    {
        $data = $this->model::withTrashed()->select(['production_no'])->whereDate('created_at', '>=', date('Y-m-01'))->whereDate('created_at', '<=', date('Y-m-t'))->orderBy('id', 'desc')->first();
        if ($data) {
            return "PP" . (int)str_replace("PP", '', $data->production_no) + 1;
        } else {
            return "PP" . date('ym') . '001';
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $editions = ProductEdition::where('product_id', $request->product_id)->where('status', true)->orderBy('name', 'asc')->get();
            return response()->json(['status' => 'success', 'editions' => $editions]);
        }

        $title = $this->create_title;
        $production_no = $this->productionNo();
        $products = Product::where('status', true)->orderBy('name', 'asc')->get();
        $stores = Store::where('status', true)->orderBy('name', 'asc')->get();
        return view("admin.{$this->path}.create", compact('title', 'production_no', 'products', 'stores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'store_id' => 'required',
            'product_id' => 'required',
            'product_edition_id' => 'required',
            'qty' => 'required'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $data = $this->model::create([
                    'store_id' => $request->store_id,
                    'production_no' => $this->productionNo(),
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'total_qty' => $request->total_qty,
                    'remarks' => $request->remarks,
                    'created_by' => Auth::id(),
                ]);

                foreach ($request->product_edition_id as $product_edition_id) {

                $productId = $request->product_id[$product_edition_id];
                $qty       = $request->qty[$product_edition_id];

                    ProductionList::create([
                        'production_id' => $data->id,
                        'store_id' => $request->store_id,
                        'product_id' => $request->product_id[$product_edition_id],
                        'product_edition_id' => $product_edition_id,
                        'qty' => $request->qty[$product_edition_id],
                    ]);
                }
                // 🔥 Stock Update
                $variant = ProductVariant::where([
                    'product_id' => $productId
                ])->first();

                if ($variant) {
                    $variant->increment('stock', $qty);
                } else {
                $product = Product::find($productId);
                    ProductVariant::create([
                        'product_id' => $productId,
                        'product_edition_id' => $product_edition_id,
                        'stock' => $qty,
                        'purchase_price' => $product->purchase_price,
                        'regular_price' => $product->regular_price,
                        'sale_price' => $product->sale_price,
                        'discount_type' => $product->discount_type,
                        'discount' => $product->discount,
                        'status' => true,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }
                // Stock update end


            });
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }

        return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Created Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function print(string $id)
    {
        $data = $this->model::findOrFail($id);
        $report_title = 'Production Voucher';
        // return view("admin.{$this->path}.print", compact('report_title', 'data'));
        $pdf = Pdf::loadView("admin.{$this->path}.print", compact('report_title', 'data'));
        return $pdf->stream('production_chalan_' . date('d_m_Y_H_i_s') . '.pdf');
    }

    public function show(string $id)
    {
        $title = 'View Production';
        $data = $this->model::findOrFail($id);
        return view("admin.{$this->path}.view", compact('title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        if ($request->ajax()) {
            $editions = ProductEdition::where('product_id', $request->product_id)->where('status', true)->orderBy('name', 'asc')->get();
            return response()->json(['status' => 'success', 'editions' => $editions]);
        }

        $additionalData = [
            'products' => Product::where('status', true)->orderBy('name', 'asc')->get(),
            'stores' => Store::where('status', true)->orderBy('name', 'asc')->get()
        ];
        return HelperClass::resourceDataEdit($this->model, $id, $this->path, $this->edit_title, $additionalData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'date' => 'required',
            'store_id' => 'required',
            'product_id' => 'required',
            'product_edition_id' => 'required',
            'qty' => 'required'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $data = $this->model::findOrFail($id);

                $data->update([
                    'store_id' => $request->store_id,
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'total_qty' => $request->total_qty,
                    'remarks' => $request->remarks,
                    'updated_by' => Auth::id(),
                ]);

                ProductionList::where('production_id', $id)->delete();
                foreach ($request->product_edition_id as $product_edition_id) {

                $productId = $request->product_id[$product_edition_id];
                $qty       = $request->qty[$product_edition_id];

                    ProductionList::create([
                        'production_id' => $data->id,
                        'store_id' => $request->store_id,
                        'product_id' => $request->product_id[$product_edition_id],
                        'product_edition_id' => $product_edition_id,
                        'qty' => $request->qty[$product_edition_id],
                    ]);
                }
                // 🔥 Stock Update
                $variant = ProductVariant::where([
                    'product_id' => $productId
                ])->first();
                
                
                if ($variant) {
                    if($variant->stock != $qty) {
                     $variant->update(['stock' => $qty]);
                    }
                } else {
                $product = Product::find($productId);
                    ProductVariant::create([
                        'product_id' => $productId,
                        'product_edition_id' => $product_edition_id,
                        'stock' => $qty,
                        'purchase_price' => $product->purchase_price,
                        'regular_price' => $product->regular_price,
                        'sale_price' => $product->sale_price,
                        'discount_type' => $product->discount_type,
                        'discount' => $product->discount,
                        'status' => true,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }
                // Stock update end

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
