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

class JournalVoucherController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'journal-voucher';
        $this->title = 'Journal Voucher';
        $this->create_title = 'Add Journal Voucher';
        $this->edit_title = 'Update Journal Voucher';
        $this->model = AccountTransactionAuto::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $model = $this->model::with('coa')
                ->where('voucher_type', 'JV')
                ->orderBy('id', 'desc')
                ->orderBy('date', 'desc')
                ->groupBy('voucher_no')
                ->selectRaw('approved, MAX(id) as id, voucher_no, MAX(date) as date, SUM(debit_amount) as total_amount');

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
                        ->where('voucher_type', 'JV')
                        ->where('debit_amount', '>', 0)
                        ->get('coa_id')->pluck('coa.head_name')->toArray();
                })
                ->addColumn('credit_head', function ($row) {
                    return $this->model::with('coa')->where('voucher_no', $row->voucher_no)
                        ->where('voucher_type', 'JV')
                        ->where('credit_amount', '>', 0)
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
        $prefix = 'JV-';

        return DB::transaction(function () use ($prefix) {

            // Fetch the maximum numeric part of voucher_no
            $last = $this->model::withTrashed()
                ->selectRaw("MAX(CAST(REPLACE(voucher_no, '$prefix', '') AS UNSIGNED)) as max_no")
                ->where('voucher_type', 'JV')
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
        $voucher_no = $this->voucherNo();
        $coas = Coa::where('transaction', '1')->orderBy('head_name', 'asc')->get();
        return view("admin.{$this->path}.create", compact('title', 'coas', 'voucher_no'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'coa_id' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $postData = [];
                $voucher_no = $this->voucherNo();
                $document = $request->hasFile('document') ? HelperClass::saveImage($request->document, 2000, $this->path) : null;
                foreach ($request->coa_id as $coa_id) {
                    if ($request->debit_amount[$coa_id] == 0 && $request->credit_amount[$coa_id] == 0) {
                        continue;
                    }
                    $postData[] = [
                        'voucher_no' => $voucher_no,
                        'voucher_type' => "JV",
                        'date' => date('Y-m-d', strtotime($request->date)),
                        'coa_id' => $coa_id,
                        'coa_head_code' => $request->head_code[$coa_id],
                        'narration' => $request->narration,
                        'debit_amount' => $request->debit_amount[$coa_id],
                        'credit_amount' => $request->credit_amount[$coa_id],
                        'document' => $document,
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
        $journalEntries = $this->model::with('coa')->where('voucher_no', $data->voucher_no)
            ->where('voucher_type', 'JV')
            ->get();

        return view("admin.{$this->path}.view", compact('title', 'data', 'journalEntries'));
    }

    public function print(string $id)
    {
        $data = $this->model::findOrFail($id);
        $journalEntries = $this->model::with('coa')->where('voucher_no', $data->voucher_no)
            ->where('voucher_type', 'JV')
            ->get();

        $report_title = $this->title;
        // return view("admin.{$this->path}.print", compact('report_title', 'data', 'journalEntries'));
        $pdf = Pdf::loadView("admin.{$this->path}.print", compact('report_title', 'data', 'journalEntries'));
        return $pdf->stream('credit_voucher_print_' . date('d_m_Y_h_i_s') . '.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = $this->edit_title;
        $data = $this->model::findOrFail($id);
        $journalEntries = $this->model::with('coa')->where('voucher_no', $data->voucher_no)
            ->where('voucher_type', 'JV')
            ->get();
        $coas = Coa::whereNotIn('id', $journalEntries->pluck('coa_id')->toArray())->where('transaction', '1')->orderBy('head_name', 'asc')->get();

        return view("admin.{$this->path}.edit", compact('title', 'data', 'journalEntries', 'coas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'date' => 'required',
            'coa_id' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $data = $this->model::findOrFail($id);

                $postData = [];
                $voucher_no = $data->voucher_no;
                $document = $request->hasFile('document') ? HelperClass::saveImage($request->document, 2000, $this->path, $data->document) : $data->document;
                $this->model::where('voucher_no', $data->voucher_no)->where('voucher_type', 'JV')->forceDelete();
                foreach ($request->coa_id as $coa_id) {
                    if ($request->debit_amount[$coa_id] == 0 && $request->credit_amount[$coa_id] == 0) {
                        continue;
                    }
                    $postData[] = [
                        'voucher_no' => $voucher_no,
                        'voucher_type' => "JV",
                        'date' => date('Y-m-d', strtotime($request->date)),
                        'coa_id' => $coa_id,
                        'coa_head_code' => $request->head_code[$coa_id],
                        'narration' => $request->narration,
                        'debit_amount' => $request->debit_amount[$coa_id],
                        'credit_amount' => $request->credit_amount[$coa_id],
                        'document' => $document,
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
                        ->where('voucher_type', 'JV')
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
                        ->where('voucher_type', 'JV')
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
                    ->where('voucher_type', 'JV')
                    ->update(['deleted_by' => Auth::id()]);
                $this->model::where('voucher_no', $data->voucher_no)
                    ->where('voucher_type', 'JV')
                    ->delete();

                $data->update(['deleted_by' => Auth::id()]);
                $data->delete();
            });
            return response()->json(['status' => 'success', 'message' => 'Successfully Deleted!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
