<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\Coa;
use App\HelperClass;
use App\Models\Sales;
use App\Models\Client;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\CollectionList;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\AccountTransactionAuto;
use App\Models\Investor;
use App\Models\PaymentList;
use App\Models\ProfitDistributionList;

class InvestorPaymentController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'investor-payment';
        $this->title = 'Investor Payment';
        $this->create_title = 'Add Payment';
        $this->edit_title = 'Update Payment';
        $this->model = Payment::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return HelperClass::resourceDataView($this->model::with(['investor'])->orderBy('id', 'desc'), null, null, $this->path, $this->title, ['transactions'], 'conditional');
    }

    /**
     * Generate Purchase No.
     */
    public function paymentNo()
    {
        $prefix = 'IP' . date('ym'); // I + YYMM, e.g. I2506

        $data = $this->model::withTrashed()
            ->select(['payment_no'])
            ->where('payment_no', 'like', $prefix . '%') // filter current month
            ->orderBy('id', 'desc')
            ->first();

        if (is_null($data)) {
            $serial = '001';
        } else {
            $lastNumber = (int)substr($data->payment_no, -3); // last 3 digits
            $serial = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }

        return $prefix . $serial;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax() && $request->investor_id) {
            $profits = ProfitDistributionList::where('investor_id', $request->investor_id)->whereColumn('amount', '>', 'paid_amount')->get();
            $profit_amount = ProfitDistributionList::where('investor_id', $request->investor_id)->sum('amount');
            $total_paid = $this->model::where('investor_id', $request->investor_id)->whereIn('payment_type', ['Payment', 'Adjust'])->sum('amount');
            $due = round($profit_amount - $total_paid, 2);

            $advance = $this->model::where('investor_id', $request->investor_id)->whereIn('payment_type', ['Advance'])->sum('amount')
                - $this->model::where('investor_id', $request->investor_id)->where('payment_type', 'Adjust')->sum('amount');

            return response()->json([
                'status' => 'success',
                'due' => round(max(0, $due), 2),
                'advance' => round($advance, 2),
                'data' => view('admin.investor-payment.partial.rows', compact('profits'))->render()
            ]);
        }

        $title = $this->create_title;
        $payment_no = $this->paymentNo();
        $investors = Investor::where('status', true)->orderBy('name', 'asc')->get();
        $cash_heads = Coa::whereHas('parent', function ($query) {
            $query->whereIn('head_name', ['Cash In Hand', 'Cash at Bank']);
        })->get();
        return view("admin.{$this->path}.create", compact('title', 'payment_no', 'investors', 'cash_heads'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'investor_id' => 'required',
            'payment_type' => 'required',
            'date' => 'required',
        ]);

        if (in_array($request->payment_type, ['Payment', 'Advance'])) {
            $request->validate(['coa_id' => 'required']);
        }

        if (in_array($request->payment_type, ['Payment', 'Adjust'])) {
            $request->validate(['distribution_list_id' => 'required']);
        }

        try {
            DB::transaction(function () use ($request) {
                $data = $this->model::create([
                    'investor_id' => $request->investor_id,
                    'coa_id' => $request->coa_id,
                    'payment_type' => $request->payment_type,
                    'payment_no' => $this->paymentNo(),
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'amount' => $request->total_amount,
                    'remarks' => $request->remarks,
                    'created_by' => Auth::id(),
                ]);

                if (in_array($request->payment_type, ['Payment', 'Adjust'])) {
                    foreach ($request->distribution_list_id as $distribution_list_id) {
                        $distributionList = ProfitDistributionList::find($distribution_list_id);
                        PaymentList::create([
                            'payment_id' => $data->id,
                            'distribution_list_id' => $distribution_list_id,
                            'invest_id' => $distributionList->invest_id,
                            'investor_id' => $request->investor_id,
                            'amount' => $request->amount[$distribution_list_id],
                        ]);
                        $distributionList->update(['paid_amount' => $distributionList->paid_amount + $request->amount[$distribution_list_id]]);
                    }
                }

                $investor = Investor::findOrFail($request->investor_id);
                if ($investor->profit_account && $request->payment_type != 'Adjust') {
                    $cash_head = Coa::findOrFail($request->coa_id);
                    $headCode = collect([
                        '0' => $investor->profit_account->head_code,
                        '1' => $cash_head->head_code
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
                            'voucher_type' => "Investor Payment",
                            'date' => date('Y-m-d', strtotime($request->date)),
                            'coa_id' => $coa->id,
                            'coa_head_code' => $headCode[$i],
                            'narration' => 'Investor Payment against payment no - ' . $data->payment_no,
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        if ($request->ajax() && $request->investor_id) {
            $data = $this->model::findOrFail($id);

            $profits = ProfitDistributionList::where('investor_id', $request->investor_id)->where(function ($query) use ($request, $data) {
                $query->whereColumn('amount', '>', 'paid_amount');
                if ($request->investor_id == $data->investor_id) {
                    $query->orWhereIn('id', $data->list->pluck('distribution_list_id')->toArray());
                }
            })->get();

            $profit_amount = ProfitDistributionList::where('investor_id', $request->investor_id)->sum('amount');
            $total_paid = $this->model::whereNot('id', $id)->where('investor_id', $request->investor_id)->whereIn('payment_type', ['Payment', 'Adjust'])->sum('amount');
            $due = round($profit_amount - $total_paid, 2);

            $advance = $this->model::whereNot('id', $id)->where('investor_id', $request->investor_id)->whereIn('payment_type', ['Advance'])->sum('amount')
                - $this->model::whereNot('id', $id)->where('investor_id', $request->investor_id)->where('payment_type', 'Adjust')->sum('amount');

            $payment_type = $request->payment_type;
            return response()->json([
                'status' => 'success',
                'due' => round(max(0, $due), 2),
                'advance' => round($advance, 2),
                'data' => view('admin.investor-payment.partial.edit-rows', compact('profits', 'data', 'advance', 'payment_type'))->render()
            ]);
        }

        $data = $this->model::findOrFail($id);
        $profits = ProfitDistributionList::where('investor_id', $data->investor_id)->where(function ($query) use ($data) {
            $query->whereColumn('amount', '>', 'paid_amount')
                ->orWhereIn('id', $data->list->pluck('distribution_list_id')->toArray());
        })->get();

        $profit_amount = ProfitDistributionList::where('investor_id', $data->investor_id)->sum('amount');
        $profit_amount = ProfitDistributionList::where('investor_id', $data->investor_id)->sum('amount');
        $total_paid = $this->model::whereNot('id', $id)->where('investor_id', $data->investor_id)->whereIn('payment_type', ['Payment', 'Adjust'])->sum('amount');
        $due = round($profit_amount - $total_paid, 2);

        $advance = $this->model::whereNot('id', $id)->where('investor_id', $data->investor_id)->whereIn('payment_type', ['Advance'])->sum('amount')
            - $this->model::whereNot('id', $id)->where('investor_id', $data->investor_id)->where('payment_type', 'Adjust')->sum('amount');

        $additionalData = [
            'investors' => Investor::where('status', true)->orderBy('name', 'asc')->get(),
            'cash_heads' => Coa::whereHas('parent', function ($query) {
                $query->whereIn('head_name', ['Cash In Hand', 'Cash at Bank']);
            })->get(),
            'due' => round(max(0, $due), 2),
            'advance' => round($advance, 2),
            'profits' => $profits,
        ];
        return HelperClass::resourceDataEdit($this->model, $id, $this->path, $this->edit_title, $additionalData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'investor_id' => 'required',
            'payment_type' => 'required',
            'date' => 'required',
        ]);

        if (in_array($request->payment_type, ['Payment', 'Advance'])) {
            $request->validate(['coa_id' => 'required']);
        }

        if (in_array($request->payment_type, ['Payment', 'Adjust'])) {
            $request->validate(['distribution_list_id' => 'required']);
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $data = $this->model::findOrFail($id);

                // Reverse Data
                foreach ($data->list as $item) {
                    $profit = ProfitDistributionList::findOrFail($item->distribution_list_id);
                    $profit->update(['paid_amount' => $profit->paid_amount - $item->amount]);
                    $item->delete();
                }
                AccountTransactionAuto::where('voucher_no', $data->payment_no)->where('voucher_type', 'Investor Payment')->forceDelete();
                // Reverse Data

                $data->update([
                    'investor_id' => $request->investor_id,
                    'coa_id' => $request->coa_id,
                    'payment_type' => $request->payment_type,
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'amount' => $request->total_amount,
                    'remarks' => $request->remarks,
                    'updated_by' => Auth::id(),
                ]);

                if (in_array($request->payment_type, ['Payment', 'Adjust'])) {
                    foreach ($request->distribution_list_id as $distribution_list_id) {
                        $distributionList = ProfitDistributionList::find($distribution_list_id);
                        PaymentList::create([
                            'payment_id' => $data->id,
                            'distribution_list_id' => $distribution_list_id,
                            'invest_id' => $distributionList->invest_id,
                            'investor_id' => $request->investor_id,
                            'amount' => $request->amount[$distribution_list_id],
                        ]);
                        $distributionList->update(['paid_amount' => $distributionList->paid_amount + $request->amount[$distribution_list_id]]);
                    }
                }

                $investor = Investor::findOrFail($request->investor_id);
                if ($investor->profit_account && $request->payment_type != 'Adjust') {
                    $cash_head = Coa::findOrFail($request->coa_id);
                    $headCode = collect([
                        '0' => $investor->profit_account->head_code,
                        '1' => $cash_head->head_code
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
                            'voucher_type' => "Investor Payment",
                            'date' => date('Y-m-d', strtotime($request->date)),
                            'coa_id' => $coa->id,
                            'coa_head_code' => $headCode[$i],
                            'narration' => 'Investor Payment against payment no - ' . $data->payment_no,
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
                        $profit = ProfitDistributionList::findOrFail($item->distribution_list_id);
                        if ($profit->amount < $profit->paid_amount + $item->amount) {
                            throw new Exception('Already paid for this profit ' . $profit->profitDistribution->serial_no ?? '');
                        }
                        $profit->update(['paid_amount' => $profit->paid_amount + $item->amount]);
                    }
                    AccountTransactionAuto::onlyTrashed()->where('voucher_no', $data->payment_no)->where('voucher_type', 'Investor Payment')->restore();

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
                        AccountTransactionAuto::onlyTrashed()->where('voucher_no', $data->payment_no)->where('voucher_type', 'Investor Payment')->forceDelete();
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
                        $profit = ProfitDistributionList::findOrFail($item->distribution_list_id);
                        $profit->update(['paid_amount' => $profit->paid_amount - $item->amount]);
                    }
                    AccountTransactionAuto::where('voucher_no', $data->payment_no)->where('voucher_type', 'Investor Payment')->update(['deleted_by' => Auth::id()]);
                    AccountTransactionAuto::where('voucher_no', $data->payment_no)->where('voucher_type', 'Investor Payment')->delete();

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
