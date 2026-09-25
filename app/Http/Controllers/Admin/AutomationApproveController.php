<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\AccountTransactionAuto;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ActionButtons\ActionButtons;

class AutomationApproveController extends Controller
{
    public $path;
    public $title;
    public $model;
    public function __construct()
    {
        $this->path = 'automation-approve';
        $this->title = 'Approve Automation';
        $this->model = AccountTransactionAuto::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $model = $this->model::whereNotIn('voucher_type', ['CV', 'DV', 'JV'])
                ->selectRaw('MAX(id) as id, voucher_no, MAX(date) as date, voucher_type, SUM(debit_amount) as amount')
                ->groupBy('voucher_no', 'voucher_type')
                ->orderBY('id', 'desc')
                ->orderBY('date', 'desc')->where('approved', false);

            return DataTables::eloquent($model)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    $checkbox = '<div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input multi_checkbox" id="' . $row->id . '" name="multi_checkbox[]" value="' . $row->id . '"><label for="' . $row->id . '" class="custom-control-label"></label></div>';
                    return $checkbox;
                })
                ->addColumn('debit_head', function ($row) {
                    return $this->model::with('coa')->where('voucher_no', $row->voucher_no)
                        ->where('voucher_type', $row->voucher_type)
                        ->where('debit_amount', '>', 0)
                        ->get('coa_id')->pluck('coa.head_name')->toArray();
                })
                ->addColumn('credit_head', function ($row) {
                    return $this->model::with('coa')->where('voucher_no', $row->voucher_no)
                        ->where('voucher_type', $row->voucher_type)
                        ->where('credit_amount', '>', 0)
                        ->get('coa_id')->pluck('coa.head_name')->toArray();
                })
                ->addColumn('actions', function ($row) {
                    $data = [
                        'id' => $row->id,
                    ];

                    $addition_btns = [
                        [
                            'parameter' => true,
                            'target' => '_self',
                            'title' => 'View Automation',
                            'route' => "admin.{$this->path}.show",
                            'icon' => '<i class="fas fa-eye"></i> View',
                            'class' => 'btn btn-outline-info btn-xs text-nowrap px-2 tt',
                        ],
                        [
                            'parameter' => true,
                            'target' => '_self',
                            'title' => 'Approve Automation',
                            'route' => "admin.{$this->path}.edit",
                            'icon' => '<i class="fa fa-thumbs-up"></i> Approve',
                            'class' => 'btn btn-outline-danger btn-xs text-nowrap approve-btn px-2 tt',
                        ]
                    ];

                    return ActionButtons::actions($data, $addition_btns);
                })
                ->rawColumns(['checkbox', 'actions'])
                ->make(true);
        }
        $title = $this->title;
        return view("admin.{$this->path}.index", compact('title'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $title = "View Automation";
        $data = $this->model::findOrFail($id);
        $transactions = $this->model::where('voucher_no', $data->voucher_no)
            ->where('voucher_type', $data->voucher_type)->get();
        return view("admin.{$this->path}.view")->with(compact('title', 'data', 'transactions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        if ($request->ajax()) {
            try {
                DB::transaction(function () use ($id, $request) {
                    $ids = $request->id ?? [$id]; // single or multiple IDs
                    foreach ($ids as $id) {
                        $data = $this->model::findOrFail($id);
                        if (!$data->approved) {
                            $this->model::where('voucher_no', $data->voucher_no)
                                ->where('voucher_type', $data->voucher_type)
                                ->update([
                                    'posted' => true,
                                    'approved' => true,
                                    'approved_by' => Auth::id(),
                                ]);
                            $transactions = $this->model::where('voucher_no', $data->voucher_no)
                                ->where('voucher_type', $data->voucher_type)->get();
                            foreach ($transactions as $item) {
                                AccountTransaction::create([
                                    'account_transaction_auto_id' => $item->id,
                                    'voucher_no' => $item->voucher_no,
                                    'voucher_type' => $item->voucher_type,
                                    'date' => $item->date,
                                    'coa_id' => $item->coa_id,
                                    'coa_head_code' => $item->coa_head_code,
                                    'narration' => $item->narration,
                                    'debit_amount' => $item->debit_amount,
                                    'credit_amount' => $item->credit_amount,
                                    'posted' => $item->posted,
                                    'document' => $item->document,
                                    'approved' => $item->approved,
                                    'approved_by' => $item->approved_by,
                                    'created_by' => $item->created_by,
                                    'updated_by' => $item->updated_by
                                ]);
                            }
                        }
                    }
                });
                return response()->json(['status' => 'success']);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }
    }
}
