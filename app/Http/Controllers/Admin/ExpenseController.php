<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AccountTransactionAuto;
use App\Models\Coa;
use App\Models\ExpenseItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'expense';
        $this->title = 'Expenses';
        $this->create_title = 'Add Expense';
        $this->edit_title = 'Update Expense';
        $this->model = Expense::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addition_btns = [[
            'parameter' => true,
            'target' => '_self',
            'title' => 'View Expense',
            'route' => "admin.{$this->path}.show",
            'icon' => '<i class="fas fa-eye"></i>',
            'class' => 'btn btn-sm btn-primary mw-fit',
        ]];

        return HelperClass::resourceDataView($this->model::with('coa')->orderBy('id', 'desc'), null, $addition_btns, $this->path, $this->title, ['transactions'], 'conditional');
    }

    /**
     * Generate Transaction No.
     */
    public function transactionNo()
    {
        $data = $this->model::withTrashed()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->latest('id')
            ->value('transaction_no');

        // remove prefix if it exists already
        $lastNumber = $data ? (int) str_replace('EXP-', '', $data) : null;

        $nextNumber = is_null($lastNumber)
            ? now()->format('ym') . '001'
            : ($lastNumber + 1);

        return 'EXP-' . $nextNumber;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = $this->create_title;

        $cashHeads = Coa::whereHas('parent', function ($query) {
            $query->whereIn('head_name', ['Cash In Hand', 'Cash at Bank']);
        })->where('transaction', 1)->orderBy('head_name', 'asc')->get();

        $debitHeads = Coa::where(function ($query) {
            $query->whereIn('head_type', ['E']);
        })->where('head_code', 'like', '403%')->where('transaction', 1)->orderBy('head_name', 'asc')->get();

        $transaction_no = $this->transactionNo();
        return view("admin.{$this->path}.create", compact('title', 'cashHeads', 'debitHeads', 'transaction_no'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cash_head' => 'required',
            'date' => 'required',
            'coa_id' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $data = $this->model::create([
                    'coa_id' => $request->cash_head,
                    'transaction_no' => $this->transactionNo(),
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'amount' => array_sum($request->debit_amount),
                    'remarks' => $request->remarks,
                    'document' => $request->hasFile('document') ? HelperClass::saveImage($request->document, 2000, $this->path) : null,
                ]);

                $head = Coa::find($request->cash_head);
                $postData = [[
                    'voucher_no' => $data->transaction_no,
                    'voucher_type' => "Expense",
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'coa_id' => $request->cash_head,
                    'coa_head_code' => $head->head_code,
                    'narration' => $request->remarks,
                    'debit_amount' => 0.00,
                    'credit_amount' => array_sum($request->debit_amount),
                    'document' => $data->document,
                    'created_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]];

                foreach ($request->coa_id as $coa_id) {
                    if ($request->debit_amount[$coa_id] == 0) {
                        continue;
                    }

                    ExpenseItem::create([
                        'expense_id'    => $data->id,
                        'coa_id'        => $coa_id,
                        'amount'        => $request->debit_amount[$coa_id]
                    ]);

                    $postData[] = [
                        'voucher_no' => $data->transaction_no,
                        'voucher_type' => "Expense",
                        'date' => date('Y-m-d', strtotime($request->date)),
                        'coa_id' => $coa_id,
                        'coa_head_code' => $request->head_code[$coa_id],
                        'credit_amount' => 0.00,
                        'debit_amount' => $request->debit_amount[$coa_id],
                        'narration' => $request->remarks,
                        'document' => $data->document,
                        'created_by' => Auth::id(),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
                AccountTransactionAuto::insert($postData);
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
        $data = $this->model::withTrashed()->findOrFail($id);
        $debitEntries = AccountTransactionAuto::withTrashed()->with('coa')->where('voucher_no', $data->transaction_no)
            ->where('voucher_type', 'Expense')
            ->where('credit_amount', 0.00)
            ->get();

        return view("admin.{$this->path}.view", compact('title', 'data', 'debitEntries'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = $this->edit_title;
        $data = $this->model::findOrFail($id);

        $cashHeads = Coa::whereHas('parent', function ($query) {
            $query->whereIn('head_name', ['Cash In Hand', 'Cash at Bank']);
        })->where('transaction', 1)->orderBy('head_name', 'asc')->get();

        $debitHeads = Coa::whereNotIn('id', $data->items->pluck('coa_id')->toArray())->where(function ($query) {
            $query->whereIn('head_type', ['E']);
        })->where('head_code', 'like', '403%')->where('transaction', 1)->orderBy('head_name', 'asc')->get();

        return view("admin.{$this->path}.edit", compact('title', 'data', 'cashHeads', 'debitHeads'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'cash_head' => 'required',
            'date' => 'required',
            'coa_id' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $data = $this->model::findOrFail($id);

                // Update main expense record
                $data->update([
                    'coa_id'        => $request->cash_head,
                    'date'          => date('Y-m-d', strtotime($request->date)),
                    'amount'        => array_sum($request->debit_amount),
                    'remarks'       => $request->remarks,
                    'document'      => $request->hasFile('document') ? HelperClass::saveImage($request->document, 2000, $this->path, $data->document) : $data->document,
                ]);

                // Remove old ExpenseItems
                $data->items()->delete();

                // Remove old transactions
                AccountTransactionAuto::where('voucher_no', $data->transaction_no)->where('voucher_type', 'Expense')->forceDelete();

                $head = Coa::find($request->cash_head);

                // First entry (credit for cash/bank)
                $postData = [[
                    'voucher_no'     => $data->transaction_no,
                    'voucher_type'   => "Expense",
                    'date'           => date('Y-m-d', strtotime($request->date)),
                    'coa_id'         => $request->cash_head,
                    'coa_head_code'  => $head->head_code,
                    'narration'      => $request->remarks,
                    'debit_amount'   => 0.00,
                    'credit_amount'  => array_sum($request->debit_amount),
                    'document'       => $data->document,
                    'updated_by'     => Auth::id(),
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now()
                ]];

                // Loop items and reinsert
                foreach ($request->coa_id as $coa_id) {
                    if ($request->debit_amount[$coa_id] == 0) {
                        continue;
                    }

                    ExpenseItem::create([
                        'expense_id' => $data->id,
                        'coa_id'     => $coa_id,
                        'amount'     => $request->debit_amount[$coa_id]
                    ]);

                    $postData[] = [
                        'voucher_no'     => $data->transaction_no,
                        'voucher_type'   => "Expense",
                        'date'           => date('Y-m-d', strtotime($request->date)),
                        'coa_id'         => $coa_id,
                        'coa_head_code'  => $request->head_code[$coa_id],
                        'credit_amount'  => 0.00,
                        'debit_amount'   => $request->debit_amount[$coa_id],
                        'narration'      => $request->remarks,
                        'document'       => $data->document,
                        'updated_by'     => Auth::id(),
                        'created_at'     => Carbon::now(),
                        'updated_at'     => Carbon::now()
                    ];
                }

                AccountTransactionAuto::insert($postData);
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
                    AccountTransactionAuto::onlyTrashed()->where('voucher_no', $data->transaction_no)->where('voucher_type', 'Expense')->restore();
                    $data->restore();
                });
                return response()->json(['status' => 'success', 'message' => 'Recovered Successfully!']);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }

        // Delete Single Item Permanent
        if (request()->has('permanent') && request('permanent') == 'true') {
            try {
                DB::transaction(function () use ($id) {
                    $data = $this->model::onlyTrashed()->findOrFail($id);
                    AccountTransactionAuto::onlyTrashed()->where('voucher_no', $data->transaction_no)->where('voucher_type', 'Expense')->forceDelete();
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
                AccountTransactionAuto::where('voucher_no', $data->transaction_no)->where('voucher_type', 'Expense')->update(['deleted_by' => Auth::id()]);
                AccountTransactionAuto::where('voucher_no', $data->transaction_no)->where('voucher_type', 'Expense')->delete();
                $data->delete();
            });
            return response()->json(['status' => 'success', 'message' => 'Successfully Deleted!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
