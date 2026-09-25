<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Coa;
use App\HelperClass;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\AccountTransactionAuto;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ActionButtons\ActionButtons;

class DebitVoucherController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path         = 'debit-voucher';
        $this->title        = 'Debit Voucher';
        $this->create_title = 'Add Debit Voucher';
        $this->edit_title   = 'Update Debit Voucher';
        $this->model        = AccountTransactionAuto::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $model = $this->model::with('coa')->where('voucher_type', 'DV')
                ->where('debit_amount', 0.00)
                ->orderBy('id', 'desc')
                ->orderBy('date', 'desc');

            $type = request('type');
            if (!empty($type) && $type == 'trash') {
                $model->onlyTrashed();
            }
            return DataTables::eloquent($model)
                ->addIndexColumn()
                ->addColumn('approved', function ($row) {
                    return $row->approved == 1 ? 'Approved' : 'Pending';
                })
                ->addColumn('debit_head', function ($row) {
                    return $this->model::with('coa')->where('voucher_no', $row->voucher_no)
                        ->where('voucher_type', 'DV')
                        ->where('credit_amount', 0.00)
                        ->get('coa_id')->pluck('coa.head_name')->toArray();
                })
                ->addColumn('actions', function ($row) {
                    $type = request('type');
                    $data = [
                        'id' => $row->id,
                        'edit' => !empty($type) && $type == 'trash' ? false : true,
                    ];

                    $delete = true;
                    $edit = true;
                    if ($row->approved == 1) {
                        $delete = false;
                        $edit = false;
                    }

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

                    return ActionButtons::actions($data, $addition_btns, $delete, $edit);
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        $title = $this->title;
        return view("admin.{$this->path}.index", compact('title'));
    }

    public function voucherNo(): string
    {
        $prefix = 'DV-';

        return DB::transaction(function () use ($prefix) {

            // Fetch the maximum numeric part of voucher_no
            $last = $this->model::withTrashed()
                ->selectRaw("MAX(CAST(REPLACE(voucher_no, '$prefix', '') AS UNSIGNED)) as max_no")
                ->where('voucher_type', 'DV')
                ->value('max_no');

            $next = $last ? ($last + 1) : 100001;

            return $prefix . $next;
        });
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = $this->create_title;

        $creditCoas = Coa::whereHas('parent', function ($query) {
            $query->whereIn('head_name', ['Cash In Hand', 'Cash at Bank']);
        })->where('transaction', '1')->orderBy('head_name', 'asc')->get();

        $coas = Coa::where(function ($query) {
            $query->whereIn('head_type', ['L', 'E']);
        })->where('transaction', '1')->orderBy('head_name', 'asc')->get();

        $voucher_no = $this->voucherNo();
        return view("admin.{$this->path}.create", compact('title', 'creditCoas', 'coas', 'voucher_no'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'credit_head' => 'required',
            'date' => 'required',
            'coa_id' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $head = Coa::find($request->credit_head);
                $total_credit = array_sum($request->debit_amount);

                $data = $this->model::create([
                    'voucher_no' => $this->voucherNo(),
                    'voucher_type' => "DV",
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'coa_id' => $request->credit_head,
                    'coa_head_code' => $head->head_code,
                    'narration' => $request->narration,
                    'credit_amount' => $total_credit,
                    'document' => $request->hasFile('document') ? HelperClass::saveImage($request->document, 2000, $this->path) : null,
                    'created_by' => Auth::id(),
                ]);

                $postData = [];
                foreach ($request->coa_id as $coa_id) {
                    if ($request->debit_amount[$coa_id] == 0) {
                        continue;
                    }
                    $postData[] = [
                        'voucher_no' => $data->voucher_no,
                        'voucher_type' => "DV",
                        'date' => date('Y-m-d', strtotime($request->date)),
                        'coa_id' => $coa_id,
                        'coa_head_code' => $request->head_code[$coa_id],
                        'narration' => $request->narration,
                        'debit_amount' => $request->debit_amount[$coa_id],
                        'document' => $data->document,
                        'created_by' => Auth::id(),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
                $this->model::insert($postData);
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
        $title = "View " . $this->title;
        $data = $this->model::findOrFail($id);
        $debitEntries = $this->model::with('coa')->where('voucher_no', $data->voucher_no)
            ->where('voucher_type', 'DV')
            ->where('credit_amount', 0.00)
            ->get();

        return view("admin.{$this->path}.view", compact('title', 'data', 'debitEntries'));
    }

    public function print(string $id)
    {
        $data = $this->model::findOrFail($id);
        $debitEntries = $this->model::with('coa')->where('voucher_no', $data->voucher_no)
            ->where('voucher_type', 'DV')
            ->where('credit_amount', 0.00)
            ->get();

        $report_title = $this->title;
        // return view("admin.{$this->path}.print", compact('report_title', 'data', 'debitEntries'));
        $pdf = Pdf::loadView("admin.{$this->path}.print", compact('report_title', 'data', 'debitEntries'));
        return $pdf->stream('debit_voucher_print_' . date('d_m_Y_h_i_s') . '.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = $this->edit_title;
        $data = $this->model::findOrFail($id);
        $debitEntries = $this->model::with('coa')->where('voucher_no', $data->voucher_no)
            ->where('voucher_type', 'DV')
            ->where('credit_amount', 0.00)
            ->get();

        $creditCoas = Coa::whereHas('parent', function ($query) {
            $query->whereIn('head_name', ['Cash In Hand', 'Cash at Bank']);
        })->where('transaction', '1')->orderBy('head_name', 'asc')->get();

        $coas = Coa::whereNotIn('id', $debitEntries->pluck('coa_id')->toArray())->where(function ($query) {
            $query->whereIn('head_type', ['L', 'E']);
        })->where('transaction', '1')->orderBy('head_name', 'asc')->get();

        return view("admin.{$this->path}.edit", compact('title', 'data', 'debitEntries', 'creditCoas', 'coas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'credit_head' => 'required',
            'date' => 'required',
            'coa_id' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $head = Coa::find($request->credit_head);
                $total_credit = array_sum($request->debit_amount);

                $data = $this->model::findOrFail($id);
                $data->update([
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'coa_id' => $request->credit_head,
                    'coa_head_code' => $head->head_code,
                    'narration' => $request->narration,
                    'credit_amount' => $total_credit,
                    'document' => $request->hasFile('document') ? HelperClass::saveImage($request->document, 2000, $this->path, $data->document) : $data->document,
                    'updated_by' => Auth::id(),
                ]);

                $this->model::where('voucher_no', $data->voucher_no)
                    ->where('voucher_type', 'DV')
                    ->where('credit_amount', 0.00)
                    ->forceDelete();

                $postData = [];
                foreach ($request->coa_id as $coa_id) {
                    if ($request->debit_amount[$coa_id] == 0) {
                        continue;
                    }
                    $postData[] = [
                        'voucher_no' => $data->voucher_no,
                        'voucher_type' => "DV",
                        'date' => date('Y-m-d', strtotime($request->date)),
                        'coa_id' => $coa_id,
                        'coa_head_code' => $request->head_code[$coa_id],
                        'narration' => $request->narration,
                        'debit_amount' => $request->debit_amount[$coa_id],
                        'document' => $data->document,
                        'created_by' => Auth::id(),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
                $this->model::insert($postData);
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
        // Recovery Deleted Data
        if (request()->has('recovery') && request('recovery') == 'true') {
            try {
                DB::transaction(function () use ($id) {
                    $data = $this->model::onlyTrashed()->findOrFail($id);
                    $this->model::onlyTrashed()->where('voucher_no', $data->voucher_no)
                        ->where('voucher_type', 'DV')
                        ->restore();
                    $data->restore();
                });
                return response()->json(['status' => 'success', 'message' => 'Recovered Successfully!']);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }

        // Delete Single Item Permanent
        if (request()->has('parmanent') && request('parmanent') == 'true') {
            try {
                DB::transaction(function () use ($id) {
                    $data = $this->model::onlyTrashed()->findOrFail($id);
                    $this->model::onlyTrashed()->where('voucher_no', $data->voucher_no)
                        ->where('voucher_type', 'DV')
                        ->forceDelete();
                    if (file_exists($data->document)) {
                        unlink($data->document);
                    }
                    $data->forceDelete();
                });
                return response()->json(['status' => 'success', 'message' => 'Permanently Deleted!']);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }

        // Delete Single Item
        try {
            DB::transaction(function () use ($id) {
                $data = $this->model::findOrFail($id);
                $this->model::where('voucher_no', $data->voucher_no)
                    ->where('voucher_type', 'DV')
                    ->update(['deleted_by' => Auth::id()]);
                $this->model::where('voucher_no', $data->voucher_no)
                    ->where('voucher_type', 'DV')
                    ->delete();
            });
            return response()->json(['status' => 'success', 'message' => 'Successfully Deleted!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
