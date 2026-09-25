<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Coa;
use App\HelperClass;
use App\Models\Store;
use App\Models\Sales;
use App\Models\Client;
use App\Models\Product;
use App\Models\SalesList;
use App\Models\Collection;
use App\Models\SalesReturn;
use Illuminate\Http\Request;
use App\Models\ProductEdition;
use App\Models\CollectionList;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\AccountTransactionAuto;
use App\Models\SalesOfficer;
use App\Models\ProductVariant;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ActionButtons\ActionButtons;

class SalesController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'sales';
        $this->title = 'Client Sales';
        $this->create_title = 'Add Sales';
        $this->edit_title = 'Update Sales';
        $this->model = Sales::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $model = $this->model::with(['client', 'store', 'tso'])->orderBy('id', 'desc');
            if (request('type') == 'trash') {
                $model->onlyTrashed();
            }
            return DataTables::eloquent($model)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    $count = true;
                    if (count($row->collections) == 0 && count($row->transactions) == 0 && count($row->returns) == 0 || count($row->collections) == 1 && $row->sale_type == 'Cash' && count($row->transactions) == 0 && count($row->returns) == 0) {
                        $count = false;
                    }

                    if ($count) {
                        return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="20" viewBox="0 0 16 20">
                            <path id="df12b5039313fc3798dfa93cfb504acd" d="M17,9V7A5,5,0,0,0,7,7V9a2.946,2.946,0,0,0-3,3v7a2.946,2.946,0,0,0,3,3H17a2.946,2.946,0,0,0,3-3V12A2.946,2.946,0,0,0,17,9ZM9,7a3,3,0,0,1,6,0V9H9Zm4.1,8.5-.1.1V17a1,1,0,0,1-2,0V15.6a1.487,1.487,0,1,1,2.1-.1Z" transform="translate(-4 -2)" fill="#9d9da6"></path>
                        </svg>';
                    } else {
                        return '<div class="custom-control custom-checkbox mx-auto">
                        <input type="checkbox" class="custom-control-input ' . (!empty(request('type')) && request('type') == "trash" ? 'trash_multi_checkbox' : 'multi_checkbox') . '" id="' . $row->id . '" name="multi_checkbox[]" value="' . $row->id . '"><label for="' . $row->id . '" class="custom-control-label"></label></div>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    $type = request('type');
                    $data = [
                        'id' => $row->id,
                        'edit' => !is_null($type) && $type == 'trash' ? false : true,
                    ];

                    $delete = false;
                    $edit = false;
                    if (count($row->collections) == 0 && count($row->transactions) == 0 && count($row->returns) == 0 || count($row->collections) == 1 && $row->sale_type == 'Cash' && count($row->transactions) == 0 && count($row->returns) == 0) {
                        $delete = true;
                        $edit = true;
                    }

                    $addition_btns = [[
                        'parameter' => true,
                        'target' => '_blank',
                        'title' => 'Print Voucher',
                        'route' => "admin.{$this->path}.show",
                        'icon' => '<span class="material-symbols-outlined">print</span>',
                        'class' => 'btn btn-sm btn-primary border-0 mw-fit px-10px fs-15',
                    ]];
                    return ActionButtons::actions($data, $addition_btns, $delete, $edit);
                })->rawColumns(['checkbox', 'actions'])->make(true);
        }

        return view("admin.{$this->path}.index", ['title' => $this->title]);
    }

    /**
     * Generate Invoice No.
     */
    public function invoiceNo()
    {
        $data = $this->model::withTrashed()->select(['invoice'])->whereDate('created_at', '>=', date('Y-m-01'))->whereDate('created_at', '<=', date('Y-m-t'))->orderBy('id', 'desc')->first();
        if ($data) {
            $trim = (int)str_replace("INVO", '', $data->invoice) + 1;
            return "INVO" . $trim;
        } else {
            return "INVO" . date('ym') . '001';
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax() && $request->has('product_id')) {
            $editions = ProductEdition::where('product_id', $request->product_id)->where('status', true)->orderBy('name', 'asc')->get();
            return response()->json(['status' => 'success', 'editions' => $editions]);
        }

        if ($request->ajax() && $request->has('client_id')) {
            $client = Client::findOrFail($request->client_id);
            $sales = $this->model::where('client_id', $request->client_id)->sum('net_amount');
            $collections = Collection::where('client_id', $request->client_id)->whereIn('collection_type', ['Payment', 'Advance'])->sum('amount');
            $returns = SalesReturn::where('client_id', $request->client_id)->sum('amount');
            $credit_limit = $client->credit_limit + $collections - $sales + $returns;
            return response()->json(['status' => 'success', 'credit_limit' => $credit_limit, 'limitation' => $client->credit_limit]);
        }

        if ($request->ajax()) {
            $stock = HelperClass::stock($request->product_edition_id, $request->store_id);
            return response()->json(['status' => 'success', 'stock' => $stock]);
        }

        $title = $this->create_title;
        $invoice = $this->invoiceNo();
        $clients = Client::where('status', true)->orderBy('name', 'asc')->get();
        $stores = Store::where('status', true)->orderBy('name', 'asc')->get();
        $salesOfficers = SalesOfficer::where('status', true)->orderBy('name', 'asc')->get();
        $cash_heads = Coa::whereHas('parent', function ($query) {
            $query->where('head_name', 'Cash In Hand')->orWhere('head_name', 'Cash at Bank');
        })->get();
        $products = Product::where('status', true)->orderBy('name', 'asc')->get();
        return view("admin.{$this->path}.create", compact('title', 'invoice', 'clients', 'stores', 'salesOfficers', 'cash_heads', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sale_type' => 'required',
            'client_id' => 'required',
            'store_id' => 'required',
            'date' => 'required',
            'product_edition_id' => 'required',
            'rate' => 'required',
            'qty' => 'required',
            'amount' => 'required'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $data = $this->model::create([
                    'client_id' => $request->client_id,
                    'store_id'  => $request->store_id,
                    'sales_officer_id' => $request->sales_officer_id,
                    'coa_id'    => $request->sale_type == 'Cash' ? $request->coa_id : null,
                    'sale_type' => $request->sale_type,
                    'invoice'   => $this->invoiceNo(),
                    'date'      => date('Y-m-d', strtotime($request->date)),
                    'amount'    => $request->total_amount,
                    'discount'  => $request->discount,
                    'tax'       => $request->tax,
                    'tax_amount' => $request->tax_amount,
                    'net_amount' => $request->net_amount,
                    'paid'      => $request->sale_type == 'Cash' ? $request->net_amount : 0.00,
                    'remarks'   => $request->remarks,
                    'created_by' => Auth::id(),
                ]);

                foreach ($request->product_edition_id as $product_edition_id) {
                    $discount = $request->total_amount > 0 ? ($request->discount / $request->total_amount) * $request->amount[$product_edition_id] : 0.00;
                    SalesList::create([
                        'sales_id' => $data->id,
                        'store_id' => $request->store_id,
                        'client_id' => $request->client_id,
                        'product_id' => $request->product_id[$product_edition_id],
                        'product_edition_id' => $product_edition_id,
                        'price' => $request->price[$product_edition_id],
                        'commission' => $request->commission[$product_edition_id],
                        'commission_amount' => $request->commission[$product_edition_id] * $request->qty[$product_edition_id],
                        'rate' => $request->rate[$product_edition_id],
                        'qty' => $request->qty[$product_edition_id],
                        'amount' => $request->amount[$product_edition_id],
                        'discount' => $discount,
                        'net_amount' => $request->amount[$product_edition_id] - $discount,
                    ]);

                     // 🔥 Stock Update
                    $productId = $request->product_id[$product_edition_id];
                    $qty = $request->qty[$product_edition_id];
                    $variant = ProductVariant::where([
                        'product_id' => $productId
                    ])->first();

                    if ($variant) {
                        $variant->decrement('stock', $qty);
                    } else {
                    $product = Product::find($productId);
                        ProductVariant::create([
                            'product_id' => $productId,
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

                    
                }

                $client = Client::find($request->client_id);
                if ($client->coa) {
                    $income_head = Coa::where('head_type', 'I')->where('head_name', 'Product Sales')->first();
                    $headCode = collect([
                        '0' => $client->coa->head_code,
                        '1' => $income_head->head_code,
                    ]);

                    $debit_amount = collect([
                        '0' => $request->net_amount,
                        '1' => 0.00
                    ]);

                    $credit_amount = collect([
                        '0' => 0.00,
                        '1' => $request->net_amount,
                    ]);

                    $countHead = count($headCode);
                    $postData = [];
                    for ($i = 0; $i < $countHead; $i++) {
                        $coa = Coa::where('head_code', $headCode[$i])->first();
                        $postData[] = [
                            'voucher_no' => $data->invoice,
                            'voucher_type' => "Client Sales",
                            'date' => date('Y-m-d', strtotime($request->date)),
                            'coa_id' => $coa->id,
                            'coa_head_code' => $headCode[$i],
                            'narration' => 'Client Sales Against Invoice No - ' . $data->invoice,
                            'debit_amount' => $debit_amount[$i],
                            'credit_amount' => $credit_amount[$i],
                            'created_by' => Auth::id(),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }
                    AccountTransactionAuto::insert($postData);
                }

                if ($request->sale_type == 'Cash') {
                    $collection_data = Collection::withTrashed()->select(['payment_no'])->whereDate('created_at', '>=', date('Y-m-01'))->whereDate('created_at', '<=', date('Y-m-t'))->orderBy('id', 'desc')->first();
                    if ($collection_data) {
                        $trim = (int)str_replace("CC", '', $collection_data->payment_no) + 1;
                        $payment_no = "CC" . $trim;
                    } else {
                        $payment_no = "CC" . date('ym') . '001';
                    }

                    $collection = Collection::create([
                        'client_id' => $request->client_id,
                        'coa_id' => $request->coa_id,
                        'sales_id' => $data->id,
                        'payment_no' => $payment_no,
                        'date' => date('Y-m-d', strtotime($request->date)),
                        'payment_type' => $request->sale_type,
                        'collection_type' => 'Payment',
                        'amount' => $request->net_amount,
                        'remarks' => 'Cash Sales',
                        'created_by' => Auth::id(),
                    ]);

                    CollectionList::create([
                        'collection_id' => $collection->id,
                        'sales_id' => $data->id,
                        'amount' => $request->net_amount,
                    ]);

                    if ($client->coa) {
                        $cash_head = Coa::findOrFail($request->coa_id);
                        $headCode = collect([
                            '0' => $cash_head->head_code,
                            '1' => $client->coa->head_code
                        ]);

                        $postData = [];
                        for ($i = 0; $i < $countHead; $i++) {
                            $coa = Coa::where('head_code', $headCode[$i])->first();
                            $postData[] = [
                                'voucher_no' => $payment_no,
                                'voucher_type' => "Client Collection",
                                'date' => date('Y-m-d', strtotime($request->date)),
                                'coa_id' => $coa->id,
                                'coa_head_code' => $headCode[$i],
                                'narration' => 'Client collection against Payment No - ' . $payment_no,
                                'debit_amount' => $debit_amount[$i],
                                'credit_amount' => $credit_amount[$i],
                                'created_by' => Auth::id(),
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }
                        AccountTransactionAuto::insert($postData);
                    }
                }
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
        $data = $this->model::withTrashed()->findOrFail($id);
        $report_title = 'Sales Invoice';
         return view("admin.{$this->path}.print", compact('report_title', 'data'));
        // $pdf = Pdf::loadView("admin.{$this->path}.print", compact('report_title', 'data'));
        // $pdf->setOptions(['defaultFont' => 'solaimanlipi']);
        // return $pdf->stream('sales_voucher_' . date('d_m_Y_H_i_s') . '.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        if ($request->ajax() && $request->has('product_id')) {
            $editions = ProductEdition::where('product_id', $request->product_id)->where('status', true)->orderBy('name', 'asc')->get();
            return response()->json(['status' => 'success', 'editions' => $editions]);
        }

        if ($request->ajax() && $request->has('client_id')) {
            $client = Client::findOrFail($request->client_id);
            $sales = $this->model::whereNot('id', $id)->where('client_id', $request->client_id)->sum('net_amount');
            $collections = Collection::whereNot('sales_id', $id)->where('client_id', $request->client_id)->whereIn('collection_type', ['Payment', 'Advance'])->sum('amount');
            $returns = SalesReturn::where('client_id', $request->client_id)->sum('amount');
            $credit_limit = $client->credit_limit + $collections - $sales + $returns;
            return response()->json(['status' => 'success', 'credit_limit' => $credit_limit, 'limitation' => $client->credit_limit]);
        }

        if ($request->ajax()) {
            $stock = HelperClass::stock($request->product_edition_id, $request->store_id);
            $stock += SalesList::where('sales_id', $id)->where('store_id', $request->store_id)->where('product_edition_id', $request->product_edition_id)->sum('qty');
            return response()->json(['status' => 'success', 'stock' => $stock]);
        }

        $data = $this->model::findOrFail($id);
        $client = Client::findOrFail($data->client_id);
        $sales = $this->model::whereNot('id', $id)->where('client_id', $data->client_id)->sum('net_amount');
        $collections = Collection::whereNot('sales_id', $id)->where('client_id', $data->client_id)->whereIn('collection_type', ['Payment', 'Advance'])->sum('amount');
        $returns = SalesReturn::where('client_id', $data->client_id)->sum('amount');
        $credit_limit = $client->credit_limit + $collections - $sales + $returns;

        $additionalData = [
            'clients'       => Client::where('status', true)->orderBy('name', 'asc')->get(),
            'stores'        => Store::where('status', true)->orderBy('name', 'asc')->get(),
            'salesOfficers' => SalesOfficer::where('status', true)->orderBy('name', 'asc')->get(),
            'cash_heads'    => Coa::whereHas('parent', function ($query) {
                $query->where('head_name', 'Cash In Hand')->orWhere('head_name', 'Cash at Bank');
            })->get(),
            'products'      => Product::where('status', true)->orderBy('name', 'asc')->get(),
            'credit_limit'  => $credit_limit,
            'limitation'    => $client->credit_limit
        ];
        return HelperClass::resourceDataEdit($this->model, $id, $this->path, $this->edit_title, $additionalData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'sale_type' => 'required',
            'client_id' => 'required',
            'store_id' => 'required',
            'date' => 'required',
            'product_edition_id' => 'required',
            'rate' => 'required',
            'qty' => 'required',
            'amount' => 'required'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $data = $this->model::findOrFail($id);

                // Reverse Data
                $payment = Collection::withTrashed()->where('sales_id', $id)->where('remarks', 'Cash Sales')->first();
                if ($payment) {
                    AccountTransactionAuto::withTrashed()->where('voucher_no', $payment->payment_no)->where('voucher_type', 'Client Collection')->forceDelete();
                    $payment->forceDelete();
                }
                AccountTransactionAuto::withTrashed()->where('voucher_no', $data->invoice)->where('voucher_type', 'Client Sales')->forceDelete();
                SalesList::where('sales_id', $id)->delete();
                // Reverse Data

                $data->update([
                    'client_id'     => $request->client_id,
                    'store_id'      => $request->store_id,
                    'sales_officer_id' => $request->sales_officer_id,
                    'coa_id'        => $request->sale_type == 'Cash' ? $request->coa_id : null,
                    'sale_type'     => $request->sale_type,
                    'date'          => date('Y-m-d', strtotime($request->date)),
                    'amount'        => $request->total_amount,
                    'discount'      => $request->discount,
                    'net_amount'    => $request->net_amount,
                    'paid'          => $request->sale_type == 'Cash' ? $request->net_amount : 0.00,
                    'remarks'       => $request->remarks,
                    'updated_by'    => Auth::id(),
                ]);

                foreach ($request->product_edition_id as $product_edition_id) {
                    $discount = $request->total_amount > 0 ? ($request->discount / $request->total_amount) * $request->amount[$product_edition_id] : 0.00;
                    SalesList::create([
                        'sales_id'  => $data->id,
                        'store_id'  => $request->store_id,
                        'client_id' => $request->client_id,
                        'product_id' => $request->product_id[$product_edition_id],
                        'product_edition_id' => $product_edition_id,
                        'price'     => $request->price[$product_edition_id],
                        'commission' => $request->commission[$product_edition_id],
                        'commission_amount' => $request->commission[$product_edition_id] * $request->qty[$product_edition_id],
                        'rate'      => $request->rate[$product_edition_id],
                        'qty'       => $request->qty[$product_edition_id],
                        'amount'    => $request->amount[$product_edition_id],
                        'discount'  => $discount,
                        'net_amount' => $request->amount[$product_edition_id] - $discount,
                    ]);
                }

                $client = Client::find($request->client_id);
                if ($client->coa) {
                    $income_head = Coa::where('head_type', 'I')->where('head_name', 'Product Sales')->first();
                    $headCode = collect([
                        '0' => $client->coa->head_code,
                        '1' => $income_head->head_code,
                    ]);

                    $debit_amount = collect([
                        '0' => $request->net_amount,
                        '1' => 0.00
                    ]);

                    $credit_amount = collect([
                        '0' => 0.00,
                        '1' => $request->net_amount,
                    ]);

                    $countHead = count($headCode);
                    $postData = [];
                    for ($i = 0; $i < $countHead; $i++) {
                        $coa = Coa::where('head_code', $headCode[$i])->first();
                        $postData[] = [
                            'voucher_no' => $data->invoice,
                            'voucher_type' => "Client Sales",
                            'date' => date('Y-m-d', strtotime($request->date)),
                            'coa_id' => $coa->id,
                            'coa_head_code' => $headCode[$i],
                            'narration' => 'Client Sales Against Invoice No - ' . $data->invoice,
                            'debit_amount' => $debit_amount[$i],
                            'credit_amount' => $credit_amount[$i],
                            'created_by' => Auth::id(),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }
                    AccountTransactionAuto::insert($postData);
                }

                if ($request->sale_type == 'Cash') {
                    $collection_data = Collection::withTrashed()->select(['payment_no'])->whereDate('created_at', '>=', date('Y-m-01'))->whereDate('created_at', '<=', date('Y-m-t'))->orderBy('id', 'desc')->first();
                    if ($collection_data) {
                        $trim = (int)str_replace("CC", '', $collection_data->payment_no) + 1;
                        $payment_no = "CC" . $trim;
                    } else {
                        $payment_no = "CC" . date('ym') . '001';
                    }

                    $collection = Collection::create([
                        'client_id' => $request->client_id,
                        'coa_id' => $request->coa_id,
                        'sales_id' => $data->id,
                        'payment_no' => $payment_no,
                        'date' => date('Y-m-d', strtotime($request->date)),
                        'payment_type' => $request->sale_type,
                        'collection_type' => 'Payment',
                        'amount' => $request->net_amount,
                        'remarks' => 'Cash Sales',
                        'created_by' => Auth::id(),
                    ]);

                    CollectionList::create([
                        'collection_id' => $collection->id,
                        'sales_id' => $data->id,
                        'amount' => $request->net_amount,
                    ]);

                    if ($client->coa) {
                        $cash_head = Coa::findOrFail($request->coa_id);
                        $headCode = collect([
                            '0' => $cash_head->head_code,
                            '1' => $client->coa->head_code
                        ]);

                        $postData = [];
                        for ($i = 0; $i < $countHead; $i++) {
                            $coa = Coa::where('head_code', $headCode[$i])->first();
                            $postData[] = [
                                'voucher_no' => $payment_no,
                                'voucher_type' => "Client Collection",
                                'date' => date('Y-m-d', strtotime($request->date)),
                                'coa_id' => $coa->id,
                                'coa_head_code' => $headCode[$i],
                                'narration' => 'Client collection against Payment No - ' . $payment_no,
                                'debit_amount' => $debit_amount[$i],
                                'credit_amount' => $credit_amount[$i],
                                'created_by' => Auth::id(),
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }
                        AccountTransactionAuto::insert($postData);
                    }
                }
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
        // Restore soft-deleted item
        if (request()->boolean('recovery')) {
            try {
                DB::transaction(function () use ($id) {
                    $data = $this->model::onlyTrashed()->findOrFail($id);

                    $payment = Collection::onlyTrashed()->where('sales_id', $id)->where('remarks', 'Cash Sales')->first();
                    if ($payment) {
                        AccountTransactionAuto::onlyTrashed()->where('voucher_no', $payment->payment_no)->where('voucher_type', 'Client Collection')->restore();
                        $payment->restore();
                    }
                    AccountTransactionAuto::onlyTrashed()->where('voucher_no', $data->invoice)->where('voucher_type', 'Client Sales')->restore();

                    $data->restore();
                });

                return response()->json(['status' => 'success', 'message' => 'Successfully Recovered!']);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }

        // Permanent delete (single or multiple)
        if (request()->boolean('parmanent')) {
            try {
                DB::transaction(function () use ($id) {
                    $ids = request()->has('id') ? request('id') : [$id];
                    foreach ((array) $ids as $deleteId) {
                        $data = $this->model::onlyTrashed()->findOrFail($deleteId);

                        $payment = Collection::onlyTrashed()->where('sales_id', $deleteId)->where('remarks', 'Cash Sales')->first();
                        if ($payment) {
                            AccountTransactionAuto::onlyTrashed()->where('voucher_no', $payment->payment_no)->where('voucher_type', 'Client Collection')->forceDelete();
                            $payment->forceDelete();
                        }
                        AccountTransactionAuto::onlyTrashed()->where('voucher_no', $data->invoice)->where('voucher_type', 'Client Sales')->forceDelete();

                        $data->forceDelete();
                    }
                });

                return response()->json(['status' => 'success', 'message' => 'Permanently Deleted!']);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }

        // Soft delete multiple items
        try {
            DB::transaction(function () use ($id) {
                $ids = request()->has('id') ? request('id') : [$id];
                foreach ((array) $ids as $deleteId) {
                    $data = $this->model::findOrFail($deleteId);

                    AccountTransactionAuto::where('voucher_no', $data->invoice)->where('voucher_type', 'Client Sales')->update(['deleted_by' => Auth::id()]);
                    AccountTransactionAuto::where('voucher_no', $data->invoice)->where('voucher_type', 'Client Sales')->delete();

                    $payment = Collection::where('sales_id', $deleteId)->where('remarks', 'Cash Sales')->first();
                    if ($payment) {
                        AccountTransactionAuto::where('voucher_no', $payment->payment_no)->where('voucher_type', 'Client Collection')->update(['deleted_by' => Auth::id()]);
                        AccountTransactionAuto::where('voucher_no', $payment->payment_no)->where('voucher_type', 'Client Collection')->delete();
                        $payment->update(['deleted_by' => Auth::id()]);
                        $payment->delete();
                    }

                    $data->update(['deleted_by' => Auth::id()]);
                    $data->delete();
                }
            });

            return response()->json(['status' => 'success', 'message' => 'Successfully Deleted!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
