<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AccountTransaction;
use App\Http\Controllers\Controller;
use App\Models\AccountTransactionAuto;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ActionButtons\ActionButtons;

class VoucherRejectController extends Controller
{
    public $path;
    public $title;
    public $model;
    public function __construct()
    {
        $this->path = 'voucher-refuse';
        $this->title = 'Refuse Voucher';
        $this->model = AccountTransaction::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $model = $this->model::whereIn('voucher_type', ['CV', 'DV', 'JV'])
                ->selectRaw('MAX(id) as id, voucher_no, MAX(date) as date, voucher_type, SUM(debit_amount) as amount')
                ->groupBy('voucher_no', 'voucher_type')
                ->orderBY('id', 'desc')
                ->orderBY('date', 'desc')->where('approved', true);

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
                            'title' => 'View Voucher',
                            'route' => "admin.{$this->path}.show",
                            'icon' => '<i class="fas fa-eye"></i> View',
                            'class' => 'btn btn-outline-info btn-xs text-nowrap px-2 tt',
                        ],
                        [
                            'parameter' => true,
                            'target' => '_self',
                            'title' => 'Refuse Voucher',
                            'route' => "admin.{$this->path}.edit",
                            'icon' => '<i class="fa fa-thumbs-down"></i> Refuse',
                            'class' => 'btn btn-outline-danger btn-xs text-nowrap refuse-btn px-2 tt',
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
        $title = "View Voucher";
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
                        AccountTransactionAuto::where('voucher_no', $data->voucher_no)
                            ->where('voucher_type', $data->voucher_type)
                            ->update([
                                'posted' => false,
                                'approved' => false,
                                'approved_by' => null,
                            ]);
                        $this->model::where('voucher_no', $data->voucher_no)
                            ->where('voucher_type', $data->voucher_type)
                            ->forceDelete();
                    }
                });
                return response()->json(['status' => 'success']);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }
    }
}
