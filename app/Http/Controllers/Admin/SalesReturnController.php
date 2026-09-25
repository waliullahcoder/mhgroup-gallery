<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\Coa;
use App\HelperClass;
use App\Models\Store;
use App\Models\Sales;
use App\Models\Client;
use App\Models\SalesList;
use App\Models\Collection;
use App\Models\SalesReturn;
use App\Models\ProductVariant;
use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SalesReturnList;
use App\Models\SalesReturnPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\AccountTransactionAuto;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ActionButtons\ActionButtons;

class SalesReturnController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'sales-return';
        $this->title = 'Sales Return';
        $this->create_title = 'Add Return';
        $this->edit_title = 'Update Return';
        $this->model = SalesReturn::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $model = $this->model::with(['client', 'store'])->orderBy('id', 'desc');
            if (request('type') == 'trash') {
                $model->onlyTrashed();
            }
            return DataTables::eloquent($model)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    if (count($row->transactions) == 0) {
                        return '<div class="custom-control custom-checkbox mx-auto">
                        <input type="checkbox" class="custom-control-input ' . (!empty(request('type')) && request('type') == "trash" ? 'trash_multi_checkbox' : 'multi_checkbox') . '" id="' . $row->id . '" name="multi_checkbox[]" value="' . $row->id . '"><label for="' . $row->id . '" class="custom-control-label"></label></div>';
                    } else {
                        return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="20" viewBox="0 0 16 20">
                            <path id="df12b5039313fc3798dfa93cfb504acd" d="M17,9V7A5,5,0,0,0,7,7V9a2.946,2.946,0,0,0-3,3v7a2.946,2.946,0,0,0,3,3H17a2.946,2.946,0,0,0,3-3V12A2.946,2.946,0,0,0,17,9ZM9,7a3,3,0,0,1,6,0V9H9Zm4.1,8.5-.1.1V17a1,1,0,0,1-2,0V15.6a1.487,1.487,0,1,1,2.1-.1Z" transform="translate(-4 -2)" fill="#9d9da6"></path>
                        </svg>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    $type = request('type');
                    $data = [
                        'id' => $row->id,
                        'edit' => !is_null($type) && $type == 'trash' ? false : true,
                    ];
                    $delete = true;
                    $edit = true;
                    if (count($row->transactions) > 0) {
                        $delete = false;
                        $edit = false;
                    }

                    $addition_btns = [[
                        'parameter' => true,
                        'target' => '_blank',
                        'title' => 'Print Voucher',
                        'route' => "admin.{$this->path}.show",
                        'icon' => '<span class="material-symbols-outlined">print</span>',
                        'class' => 'btn btn-sm btn-primary border-0 px-10px fs-15 mw-fit',
                    ]];
                    return ActionButtons::actions($data, $addition_btns, $delete, $edit);
                })->rawColumns(['checkbox', 'actions'])->make(true);
        }

        return view("admin.{$this->path}.index", ['title' => $this->title]);
    }

    /**
     * Generate Purchase No.
     */
    public function returnNo()
    {
        $data = $this->model::withTrashed()->select(['return_no'])->whereDate('created_at', '>=', date('Y-m-01'))->whereDate('created_at', '<=', date('Y-m-t'))->orderBy('id', 'desc')->first();
        if ($data) {
            return "SR" . (int)str_replace("SR", '', $data->return_no) + 1;
        } else {
            return  "SR" . date('ym') . '001';
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax() && $request->has('sales_id')) {
            $list = SalesList::with('sales')->where('sales_id', $request->sales_id)->whereColumn('qty', '>', 'return_qty')->get();
            return response()->json([
                'status' => 'success',
                'data' => view('admin.sales-return.partial.option', compact('list'))->render(),
            ]);
        }

        if ($request->ajax()) {
            $sales = Sales::where('client_id', $request->client_id)->whereColumn('net_amount', '>', 'return_amount')->orderBy('id', 'desc')->get();
            return response()->json(['status' => 'success', 'sales' => $sales]);
        }

        $title = $this->create_title;
        $return_no = $this->returnNo();
        $clients = Client::where('status', true)->orderBy('name', 'asc')->get();
        $stores = Store::where('status', true)->orderBy('name', 'asc')->get();
        return view("admin.{$this->path}.create", compact('title', 'return_no', 'clients', 'stores'));
    }

    /**
     * Generate Payment No.
     */
    public function paymentNo()
    {
        $data = Collection::withTrashed()->select(['payment_no'])->whereDate('created_at', '>=', date('Y-m-01'))->whereDate('created_at', '<=', date('Y-m-t'))->orderBy('id', 'desc')->first();
        if ($data) {
            return "CC" . (int)str_replace("CC", '', $data->payment_no) + 1;
        } else {
            return "CC" . date('ym') . '001';
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'store_id' => 'required',
            'date' => 'required',
            'sales_id' => 'required',
            'sales_list_id' => 'required',
            'rate' => 'required',
            'qty' => 'required',
            'amount' => 'required'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $data = $this->model::create([
                    'client_id' => $request->client_id,
                    'store_id' => $request->store_id,
                    'return_no' => $this->returnNo(),
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'amount' => $request->total_amount,
                    'remarks' => $request->remarks,
                    'created_by' => Auth::id(),
                ]);

                $sales_id = [];
                foreach ($request->sales_list_id as $sales_list_id) {
                    if ($request->qty[$sales_list_id] > 0) {
                        $sales_id[] = $request->sales_id;
                        SalesReturnList::create([
                            'sales_return_id' => $data->id,
                            'client_id' => $request->client_id,
                            'store_id' => $request->store_id,
                            'sales_id' => $request->sales_id[$sales_list_id],
                            'sales_list_id' => $sales_list_id,
                            'product_id' => $request->product_id[$sales_list_id],
                            'product_edition_id' => $request->product_edition_id[$sales_list_id],
                            'rate' => $request->rate[$sales_list_id],
                            'qty' => $request->qty[$sales_list_id],
                            'amount' => $request->amount[$sales_list_id]
                        ]);

                        $sales_list = SalesList::find($sales_list_id);
                        $sales_list->update([
                            'return_qty' => $sales_list->return_qty + $request->qty[$sales_list_id],
                            'return_amount' => $sales_list->return_amount + $request->amount[$sales_list_id]
                        ]);
                        $sales = Sales::find($request->sales_id[$sales_list_id]);
                        $sales->update([
                            'return_amount' => $sales->return_amount + $request->amount[$sales_list_id]
                        ]);

                    // 🔥 Stock Update
                    $productId = $request->product_id[$sales_list_id];
                    $qty = $request->qty[$sales_list_id];
                    $variant = ProductVariant::where([
                        'product_id' => $productId
                    ])->first();

                    if ($variant) {
                        $variant->increment('stock', $qty);
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

                }

                $client = Client::find($request->client_id);
                if ($client->coa) {
                    $income_head = Coa::where('head_type', 'I')->where('head_name', 'Sales Return')->first();
                    $headCode = collect([
                        '0' => $income_head->head_code,
                        '1' => $client->coa->head_code
                    ]);

                    $debit_amount = collect([
                        '0' => $request->total_amount,
                        '1' => 0.00
                    ]);

                    $credit_amount = collect([
                        '0' => 0.00,
                        '1' => $request->total_amount,
                    ]);

                    $countHead = count($headCode);
                    $postData = [];
                    for ($i = 0; $i < $countHead; $i++) {
                        $coa = Coa::where('head_code', $headCode[$i])->first();
                        $postData[] = [
                            'voucher_no' => $data->return_no,
                            'voucher_type' => "Sales Return",
                            'date' => date('Y-m-d', strtotime($request->date)),
                            'coa_id' => $coa->id,
                            'coa_head_code' => $headCode[$i],
                            'narration' => 'Sales Return against Return No - ' . $data->return_no,
                            'debit_amount' => $debit_amount[$i],
                            'credit_amount' => $credit_amount[$i],
                            'created_by' => Auth::id(),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }
                    AccountTransactionAuto::insert($postData);
                }

                $previous_payment = 0;
               $sales = Sales::whereIn('id', collect($sales_id)->flatten()->unique()->toArray())->get();
                foreach ($sales as $item) {
                    $balance = $item->paid + $item->return_amount - $item->net_amount - $item->return_paid;
                    if ($balance > 0) {
                        $sales = Sales::find($item->id);
                        $sales->update([
                            'return_paid' => $sales->return_paid + $balance
                        ]);
                        SalesReturnPayment::create([
                            'sales_return_id' => $data->id,
                            'sales_id' => $item->id,
                            'amount' => $balance
                        ]);
                        $previous_payment +=  $balance;
                    }
                }

                if ($previous_payment > 0) {
                    Collection::create([
                        'client_id' => $request->client_id,
                        'sales_return_id' => $data->id,
                        'payment_no' => $this->paymentNo(),
                        'date' => date('Y-m-d', strtotime($request->date)),
                        'payment_type' => 'Return',
                        'collection_type' => 'Return',
                        'amount' => $previous_payment,
                        'remarks' => 'Paid on Return Products',
                        'created_by' => Auth::id(),
                    ]);
                }
            });
        } catch (\Exception $e) {
            dd($e);
            return back()->withErrors($e->getMessage());
        }

        return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Created Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = $this->model::findOrFail($id);
        $report_title = 'Sales Return Voucher';
        // return view("admin.{$this->path}.print", compact('report_title', 'data'));
        $pdf = Pdf::loadView("admin.{$this->path}.print", compact('report_title', 'data'));
        return $pdf->stream('sales_return_chalan_' . date('d_m_Y_H_i_s') . '.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        if ($request->ajax() && $request->has('sales_id')) {
            $data = SalesReturn::find($id);
            $list = SalesList::where('sales_id', $request->sales_id)
                ->where(function ($query) use ($request, $data) {
                    $query->whereColumn('qty', '>', 'return_qty');
                    if ($request->client_id == $data->client_id) {
                        $query->orWhereIn('id', $data->list->pluck('sales_list_id'));
                    }
                })->get();

            return response()->json([
                'status' => 'success',
                'data' => view('admin.sales-return.partial.option', compact('list', 'data'))->render(),
            ]);
        }

        if ($request->ajax()) {
            $data = SalesReturn::find($id);
            $sales = Sales::where('client_id', $request->client_id)->where(function ($query) use ($request, $data) {
                $query->whereColumn('net_amount', '>', 'return_amount');
                if ($request->client_id == $data->client_id) {
                    $query->orWhereIn('id', $data->list->pluck('sales_id'));
                }
            })->orderBy('id', 'desc')->get();
            return response()->json(['status' => 'success', 'sales' => $sales]);
        }

        $additionalData = [
            'clients' => Client::where('status', true)->orderBy('name', 'asc')->get(),
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
            'client_id' => 'required',
            'store_id' => 'required',
            'date' => 'required',
            'sales_id' => 'required',
            'sales_list_id' => 'required',
            'rate' => 'required',
            'qty' => 'required',
            'amount' => 'required'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $data = $this->model::findOrFail($id);

                // Reverse Data
                AccountTransactionAuto::withTrashed()->where('voucher_no', $data->return_no)->where('voucher_type', 'Sales Return')->forceDelete();
                foreach ($data->list as $item) {
                    $sales_list = SalesList::findOrFail($item->sales_list_id);
                    $sales_list->update([
                        'return_qty' => $sales_list->return_qty - $item->qty,
                        'return_amount' => $sales_list->return_amount - $item->amount
                    ]);

                    $sales = Sales::find($item->sales_id);
                    $sales->update([
                        'return_amount' => $sales->return_amount - $item->amount
                    ]);
                }

                foreach ($data->collections as $item) {
                    $sales = Sales::find($item->sales_id);
                    $sales->update([
                        'return_paid' => $sales->return_paid - $item->amount
                    ]);
                }

                Collection::where('sales_return_id', $id)->forceDelete();
                SalesReturnPayment::where('sales_return_id', $id)->delete();
                SalesReturnList::where('sales_return_id', $id)->delete();
                // Reverse Data

                $data->update([
                    'client_id' => $request->client_id,
                    'store_id' => $request->store_id,
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'amount' => $request->total_amount,
                    'remarks' => $request->remarks,
                    'updated_by' => Auth::id(),
                ]);

                $sales_id = [];
                foreach ($request->sales_list_id as $sales_list_id) {
                    if ($request->qty[$sales_list_id] > 0) {
                        $sales_id[] = $request->sales_id;
                        SalesReturnList::create([
                            'sales_return_id' => $data->id,
                            'client_id' => $request->client_id,
                            'store_id' => $request->store_id,
                            'sales_id' => $request->sales_id[$sales_list_id],
                            'sales_list_id' => $sales_list_id,
                            'product_id' => $request->product_id[$sales_list_id],
                            'product_edition_id' => $request->product_edition_id[$sales_list_id],
                            'rate' => $request->rate[$sales_list_id],
                            'qty' => $request->qty[$sales_list_id],
                            'amount' => $request->amount[$sales_list_id]
                        ]);

                        $sales_list = SalesList::find($sales_list_id);
                        $sales_list->update([
                            'return_qty' => $sales_list->return_qty + $request->qty[$sales_list_id],
                            'return_amount' => $sales_list->return_amount + $request->amount[$sales_list_id]
                        ]);
                        $sales = Sales::find($request->sales_id[$sales_list_id]);
                        $sales->update([
                            'return_amount' => $sales->return_amount + $request->amount[$sales_list_id]
                        ]);
                    }
                }

                $client = Client::find($request->client_id);
                if ($client->coa) {
                    $income_head = Coa::where('head_type', 'I')->where('head_name', 'Sales Return')->first();
                    $headCode = collect([
                        '0' => $income_head->head_code,
                        '1' => $client->coa->head_code
                    ]);

                    $debit_amount = collect([
                        '0' => $request->total_amount,
                        '1' => 0.00
                    ]);

                    $credit_amount = collect([
                        '0' => 0.00,
                        '1' => $request->total_amount,
                    ]);

                    $countHead = count($headCode);
                    $postData = [];
                    for ($i = 0; $i < $countHead; $i++) {
                        $coa = Coa::where('head_code', $headCode[$i])->first();
                        $postData[] = [
                            'voucher_no' => $data->return_no,
                            'voucher_type' => "Sales Return",
                            'date' => date('Y-m-d', strtotime($request->date)),
                            'coa_id' => $coa->id,
                            'coa_head_code' => $headCode[$i],
                            'narration' => 'Sales Return against Return No - ' . $data->return_no,
                            'debit_amount' => $debit_amount[$i],
                            'credit_amount' => $credit_amount[$i],
                            'created_by' => Auth::id(),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }
                    AccountTransactionAuto::insert($postData);
                }

                $previous_payment = 0;
                $sales = Sales::whereIn('id', array_unique($sales_id))->get();
                foreach ($sales as $item) {
                    $balance = $item->paid + $item->return_amount - $item->net_amount - $item->return_paid;
                    if ($balance > 0) {
                        $sales = Sales::find($item->id);
                        $sales->update([
                            'return_paid' => $sales->return_paid + $balance
                        ]);
                        SalesReturnPayment::create([
                            'sales_return_id' => $data->id,
                            'sales_id' => $item->id,
                            'amount' => $balance
                        ]);
                        $previous_payment +=  $balance;
                    }
                }

                if ($previous_payment > 0) {
                    Collection::create([
                        'client_id' => $request->client_id,
                        'sales_return_id' => $data->id,
                        'payment_no' => $this->paymentNo(),
                        'date' => date('Y-m-d', strtotime($request->date)),
                        'payment_type' => 'Return',
                        'collection_type' => 'Return',
                        'amount' => $previous_payment,
                        'remarks' => 'Paid on Return Products',
                        'created_by' => Auth::id(),
                    ]);
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

                    foreach ($data->list as $item) {
                        $sales_list = SalesList::findOrFail($item->sales_list_id);
                        if ($sales_list->return_qty + $item->qty > $sales_list->qty) {
                            throw new Exception('Something went wrong!');
                        }
                        $sales_list->update([
                            'return_qty' => $sales_list->return_qty + $item->qty,
                            'return_amount' => $sales_list->return_amount + $item->amount
                        ]);

                        $sales = Sales::find($item->sales_id);
                        $sales->update([
                            'return_amount' => $sales->return_amount + $item->amount
                        ]);
                    }

                    foreach ($data->collections as $item) {
                        $sales = Sales::find($item->sales_id);
                        if ($sales->net_amount < $sales->return_paid + $item->amount) {
                            throw new Exception('Something went wrong!');
                        }
                        $sales->update([
                            'return_paid' => $sales->return_paid + $item->amount
                        ]);
                    }

                    $collection = Collection::onlyTrashed()->where('sales_return_id', $id)->first();
                    if ($collection) {
                        $collection->restore();
                    }
                    AccountTransactionAuto::onlyTrashed()->where('voucher_no', $data->invoice)->where('voucher_type', 'Sales Return')->restore();
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
                        $collection = Collection::onlyTrashed()->where('sales_return_id', $deleteId)->first();
                        if ($collection) {
                            $collection->forceDelete();
                        }
                        AccountTransactionAuto::onlyTrashed()->where('voucher_no', $data->return_no)->where('voucher_type', 'Sales Return')->forceDelete();
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

                    foreach ($data->list as $item) {
                        $sales_list = SalesList::findOrFail($item->sales_list_id);
                        $sales_list->update([
                            'return_qty' => $sales_list->return_qty - $item->qty,
                            'return_amount' => $sales_list->return_amount - $item->amount
                        ]);

                        $sales = Sales::find($item->sales_id);
                        $sales->update([
                            'return_amount' => $sales->return_amount - $item->amount
                        ]);
                    }

                    foreach ($data->collections as $item) {
                        $sales = Sales::find($item->sales_id);
                        $sales->update([
                            'return_paid' => $sales->return_paid - $item->amount
                        ]);
                    }
                    $collection = Collection::where('sales_return_id', $deleteId)->first();
                    if ($collection) {
                        $collection->update(['deleted_by' => Auth::id()]);
                        $collection->delete();
                    }

                    AccountTransactionAuto::where('voucher_no', $data->return_no)->where('voucher_type', 'Sales Return')->update(['deleted_by' => Auth::id()]);
                    AccountTransactionAuto::where('voucher_no', $data->return_no)->where('voucher_type', 'Sales Return')->delete();

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
