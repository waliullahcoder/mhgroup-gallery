<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\Coa;
use App\HelperClass;
use App\Models\Sales;
use App\Models\Client;
use App\Models\Collection;
use Illuminate\Http\Request;
use App\Models\CollectionList;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\AccountTransactionAuto;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ActionButtons\ActionButtons;

class CollectionController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'collection';
        $this->title = 'Client Collections';
        $this->create_title = 'Add Collection';
        $this->edit_title = 'Update Collection';
        $this->model = Collection::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $model = $this->model::with(['client'])->whereNot('collection_type', 'Return')->orderBy('id', 'desc');
            if (request('type') == 'trash') {
                $model->onlyTrashed();
            }
            return DataTables::eloquent($model)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    if (count($row->transactions) == 0 && is_null($row->sales_id)) {
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
                    if (count($row->transactions) > 0 || !is_null($row->sales_id)) {
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
    public function paymentNo()
    {
        $data = $this->model::withTrashed()->select(['payment_no'])->whereDate('created_at', '>=', date('Y-m-01'))->whereDate('created_at', '<=', date('Y-m-t'))->orderBy('id', 'desc')->first();
        if ($data) {
            return "CC" . (int)str_replace("CC", '', $data->payment_no) + 1;
        } else {
            return "CC" . date('ym') . '001';
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax() && $request->payment_type) {
            $cash_heads = Coa::whereHas('parent', function ($query) use ($request) {
                if ($request->payment_type == 'Cash') {
                    $query->where('head_name', 'Cash In Hand');
                }
                if ($request->payment_type == 'Bank') {
                    $query->where('head_name', 'Cash at Bank');
                }
            })->get();
            return response()->json(['status' => 'success', 'cash_heads' => $cash_heads]);
        }

        if ($request->ajax() && $request->client_id) {
            $sales = Sales::where('client_id', $request->client_id)->whereColumn('net_amount', '>', DB::raw('paid+return_amount'))->get();
            $sales_amount = Sales::where('client_id', $request->client_id)->sum('net_amount');
            $total_paid = $this->model::where('client_id', $request->client_id)->whereIn('collection_type', ['Payment', 'Adjust'])->sum('amount');
            $due = round($sales_amount - $total_paid, 2);

            $advance = $this->model::where('client_id', $request->client_id)->whereIn('collection_type', ['Return', 'Advance'])->sum('amount')
                - $this->model::where('client_id', $request->client_id)->where('collection_type', 'Adjust')->sum('amount');

            return response()->json([
                'status' => 'success',
                'due' => round(max(0, $due), 2),
                'advance' => round($advance, 2),
                'data' => view('admin.collection.partial.rows', compact('sales'))->render()
            ]);
        }

        $title = $this->create_title;
        $payment_no = $this->paymentNo();
        $clients = Client::where('status', true)->orderBy('name', 'asc')->get();
        $cash_heads = Coa::whereHas('parent', function ($query) {
            $query->where('head_name', 'Cash In Hand');
        })->get();
        return view("admin.{$this->path}.create", compact('title', 'payment_no', 'clients', 'cash_heads'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'payment_type' => 'required',
            'date' => 'required',
            'collection_type' => 'required'
        ]);

        if (in_array($request->collection_type, ['Payment', 'Advance'])) {
            $request->validate(['coa_id' => 'required']);
        }

        if (in_array($request->collection_type, ['Payment', 'Adjust'])) {
            $request->validate(['sales_id' => 'required']);
        }

        try {
            DB::transaction(function () use ($request) {
                $data = $this->model::create([
                    'client_id' => $request->client_id,
                    'coa_id' => $request->coa_id,
                    'payment_no' => $this->paymentNo(),
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'payment_type' => $request->payment_type,
                    'collection_type' => $request->collection_type,
                    'amount' => $request->total_amount,
                    'remarks' => $request->remarks,
                    'created_by' => Auth::id(),
                ]);

                if (in_array($request->collection_type, ['Payment', 'Adjust'])) {
                    foreach ($request->sales_id as $sales_id) {
                        CollectionList::create([
                            'collection_id' => $data->id,
                            'sales_id' => $sales_id,
                            'amount' => $request->amount[$sales_id],
                        ]);
                        $sales = Sales::findOrFail($sales_id);
                        $sales->update(['paid' => $sales->paid + $request->amount[$sales_id]]);
                    }
                }

                $client = Client::findOrFail($request->client_id);
                if ($client->coa && $request->collection_type != 'Adjust') {
                    $cash_head = Coa::findOrFail($request->coa_id);
                    $headCode = collect([
                        '0' => $cash_head->head_code,
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

                    $postData = [];
                    $countHead = count($headCode);
                    for ($i = 0; $i < $countHead; $i++) {
                        $coa = Coa::where('head_code', $headCode[$i])->first();
                        $postData[] = [
                            'voucher_no' => $data->payment_no,
                            'voucher_type' => "Client Collection",
                            'date' => date('Y-m-d', strtotime($request->date)),
                            'coa_id' => $coa->id,
                            'coa_head_code' => $headCode[$i],
                            'narration' => 'Client collection against payment no - ' . $data->payment_no,
                            'debit_amount' => $debit_amount[$i],
                            'credit_amount' => $credit_amount[$i],
                            'created_by' => Auth::id(),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }
                    AccountTransactionAuto::insert($postData);
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
        $report_title = 'Collection Voucher';
        $data = $this->model::findOrFail($id);
        // return view("admin.{$this->path}.print", compact('report_title', 'data'));
        $pdf = Pdf::loadView("admin.{$this->path}.print", compact('report_title', 'data'));
        return $pdf->stream('collection_voucher_' . date('d_m_Y_H_i_s') . '.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        if ($request->ajax() && $request->payment_type) {
            $cash_heads = Coa::whereHas('parent', function ($query) use ($request) {
                if ($request->payment_type == 'Cash') {
                    $query->where('head_name', 'Cash In Hand');
                }
                if ($request->payment_type == 'Bank') {
                    $query->where('head_name', 'Cash at Bank');
                }
            })->get();
            return response()->json(['status' => 'success', 'cash_heads' => $cash_heads]);
        }

        if ($request->ajax() && $request->client_id) {
            $data = $this->model::findOrFail($id);

            $sales = Sales::where('client_id', $request->client_id)->where(function ($query) use ($request, $data) {
                $query->whereColumn('net_amount', '>', DB::raw('paid+return_amount'));
                if ($request->client_id == $data->client_id) {
                    $query->orWhereIn('id', $data->list->pluck('sales_id')->toArray());
                }
            })->get();
            $sales_amount = Sales::where('client_id', $request->client_id)->sum('net_amount');
            $total_paid = $this->model::whereNot('id', $id)->where('client_id', $request->client_id)->whereIn('collection_type', ['Payment', 'Adjust'])->sum('amount');
            $due = round($sales_amount - $total_paid, 2);

            $advance = $this->model::whereNot('id', $id)->where('client_id', $request->client_id)->whereIn('collection_type', ['Return', 'Advance'])->sum('amount')
                - $this->model::whereNot('id', $id)->where('client_id', $request->client_id)->where('collection_type', 'Adjust')->sum('amount');

            $type = $request->type;
            return response()->json([
                'status' => 'success',
                'due' => round(max(0, $due), 2),
                'advance' => round($advance, 2),
                'data' => view('admin.collection.partial.edit-rows', compact('sales', 'data', 'advance', 'type'))->render()
            ]);
        }

        $data = $this->model::findOrFail($id);
        $sales = Sales::where('client_id', $data->client_id)->where(function ($query) use ($data) {
            $query->whereColumn('net_amount', '>', DB::raw('paid+return_amount'))->orWhereIn('id', $data->list->pluck('sales_id')->toArray());
        })->get();
        $sales_amount = Sales::where('client_id', $data->client_id)->sum('net_amount');
        $total_paid = $this->model::whereNot('id', $id)->where('client_id', $data->client_id)->whereIn('collection_type', ['Payment', 'Adjust'])->sum('amount');
        $due = round($sales_amount - $total_paid, 2);

        $advance = $this->model::whereNot('id', $id)->where('client_id', $data->client_id)->whereIn('collection_type', ['Return', 'Advance'])->sum('amount')
            - $this->model::whereNot('id', $id)->where('client_id', $data->client_id)->where('collection_type', 'Adjust')->sum('amount');

        $additionalData = [
            'clients' => Client::where('status', true)->orderBy('name', 'asc')->get(),
            'cash_heads' => Coa::whereHas('parent', function ($query) use ($data) {
                if ($data->payment_type == 'Cash') {
                    $query->where('head_name', 'Cash In Hand');
                }
                if ($data->payment_type == 'Bank') {
                    $query->where('head_name', 'Cash at Bank');
                }
            })->get(),
            'due' => round(max(0, $due), 2),
            'advance' => round($advance, 2),
            'sales' => $sales,
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
            'payment_type' => 'required',
            'date' => 'required',
            'collection_type' => 'required'
        ]);

        if (in_array($request->collection_type, ['Payment', 'Advance'])) {
            $request->validate(['coa_id' => 'required']);
        }

        if (in_array($request->collection_type, ['Payment', 'Adjust'])) {
            $request->validate(['sales_id' => 'required']);
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $data = $this->model::findOrFail($id);

                // Reverse Data
                foreach ($data->list as $item) {
                    $sales = Sales::findOrFail($item->sales_id);
                    $sales->update(['paid' => $sales->paid - $item->amount]);
                    $item->delete();
                }
                AccountTransactionAuto::where('voucher_no', $data->payment_no)->where('voucher_type', 'Client Collection')->forceDelete();
                // Reverse Data

                $data->update([
                    'client_id' => $request->client_id,
                    'coa_id' => $request->coa_id,
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'payment_type' => $request->payment_type,
                    'collection_type' => $request->collection_type,
                    'amount' => $request->total_amount,
                    'remarks' => $request->remarks,
                    'updated_by' => Auth::id(),
                ]);

                if (in_array($request->collection_type, ['Payment', 'Adjust'])) {
                    foreach ($request->sales_id as $sales_id) {
                        CollectionList::create([
                            'collection_id' => $data->id,
                            'sales_id' => $sales_id,
                            'amount' => $request->amount[$sales_id],
                        ]);
                        $sales = Sales::findOrFail($sales_id);
                        $sales->update(['paid' => $sales->paid + $request->amount[$sales_id]]);
                    }
                }

                $client = Client::findOrFail($request->client_id);
                if ($client->coa && $request->collection_type != 'Adjust') {
                    $cash_head = Coa::findOrFail($request->coa_id);
                    $headCode = collect([
                        '0' => $cash_head->head_code,
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

                    $postData = [];
                    $countHead = count($headCode);
                    for ($i = 0; $i < $countHead; $i++) {
                        $coa = Coa::where('head_code', $headCode[$i])->first();
                        $postData[] = [
                            'voucher_no' => $data->payment_no,
                            'voucher_type' => "Client Collection",
                            'date' => date('Y-m-d', strtotime($request->date)),
                            'coa_id' => $coa->id,
                            'coa_head_code' => $headCode[$i],
                            'narration' => 'Client collection against payment no - ' . $data->payment_no,
                            'debit_amount' => $debit_amount[$i],
                            'credit_amount' => $credit_amount[$i],
                            'created_by' => Auth::id(),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }
                    AccountTransactionAuto::insert($postData);
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
                        $sales = Sales::findOrFail($item->sales_id);
                        if ($sales->net_amount < $sales->paid + $item->amount) {
                            throw new Exception('Already paid for this invoice ' . $sales->invoice);
                        }
                        $sales->update(['paid' => $sales->paid + $item->amount]);
                    }
                    AccountTransactionAuto::onlyTrashed()->where('voucher_no', $data->payment_no)->where('voucher_type', 'Client Collection')->restore();

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
                        AccountTransactionAuto::onlyTrashed()->where('voucher_no', $data->payment_no)->where('voucher_type', 'Client Collection')->forceDelete();
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
                        $sales = Sales::findOrFail($item->sales_id);
                        $sales->update(['paid' => $sales->paid - $item->amount]);
                    }
                    AccountTransactionAuto::where('voucher_no', $data->payment_no)->where('voucher_type', 'Client Collection')->update(['deleted_by' => Auth::id()]);
                    AccountTransactionAuto::where('voucher_no', $data->payment_no)->where('voucher_type', 'Client Collection')->delete();

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
