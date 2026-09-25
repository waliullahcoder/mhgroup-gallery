<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\Coa;
use App\HelperClass;
use App\Models\Invest;
use App\Models\Investor;
use Illuminate\Http\Request;
use App\Models\InvestSattlement;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\InvestSattlementList;
use Illuminate\Support\Facades\Auth;
use App\Models\AccountTransactionAuto;

class InvestSattlementController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'invest-sattlement';
        $this->title = 'Invest Sattlements';
        $this->create_title = 'Add Sattlement';
        $this->edit_title = 'Update Sattlement';
        $this->model = InvestSattlement::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addition_btns = [[
            'parameter' => true,
            'target' => '_self',
            'title' => 'View',
            'route' => "admin.{$this->path}.show",
            'icon' => '<i class="fas fa-eye"></i>',
            'class' => 'btn btn-sm btn-primary mw-fit',
        ]];

        return HelperClass::resourceDataView($this->model::with(['investor'])->orderBy('id', 'desc'), null, $addition_btns, $this->path, $this->title, 'transactions', 'conditional');
    }

    public function sattlementNo()
    {
        $prefix = 'IS' . date('ym'); // I + YYMM, e.g. I2506

        $data = $this->model::withTrashed()
            ->select(['sattlement_no'])
            ->where('sattlement_no', 'like', $prefix . '%') // filter current month
            ->orderBy('id', 'desc')
            ->first();

        if (is_null($data)) {
            $serial = '001';
        } else {
            $lastNumber = (int)substr($data->sattlement_no, -3); // last 3 digits
            $serial = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }

        return $prefix . $serial;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $invests = Invest::where('investor_id', $request->investor_id)->where('sattled', false)->get();
            return response()->json([
                'status' => 'success',
                'data' => view('admin.invest-sattlement.partial.rows', compact('invests'))->render()
            ]);
        }

        $title = $this->create_title;
        $investors = Investor::where('status', true)->orderBy('name', 'asc')->get();
        $cashHeads = Coa::whereHas('parent', function ($query) {
            $query->whereIn('head_name', ['Cash In Hand', 'Cash at Bank']);
        })->get();
        $sattlement_no = $this->sattlementNo();
        return view("admin.{$this->path}.create", compact('title', 'investors', 'cashHeads', 'sattlement_no'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'coa_id' => 'required',
            'investor_id' => 'required',
            'invest_id' => 'required'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $investQty = 0;
                $investAmount = 0;
                foreach ($request->invest_id as $invest_id) {
                    $invest = Invest::find($invest_id);
                    $investQty += $invest->qty;
                    $investAmount += $invest->amount;
                }

                $data = $this->model::create([
                    'investor_id' => $request->investor_id,
                    'coa_id' => $request->coa_id,
                    'sattlement_no' => $this->sattlementNo(),
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'invest_qty' => $investQty,
                    'invest_amount' => $investAmount,
                    'payment' => $investAmount,
                    'remarks' => $request->remarks,
                    'created_by' => Auth::id()
                ]);

                foreach ($request->invest_id as $invest_id) {
                    $invest = Invest::find($invest_id);
                    InvestSattlementList::create([
                        'invest_sattlement_id' => $data->id,
                        'investor_id' => $invest->investor_id,
                        'invest_id' => $invest_id,
                        'product_id' => $invest->product_id,
                        'invest_qty' => $invest->qty,
                        'invest_amount' => $invest->amount,
                        'payment' => $invest->amount
                    ]);
                    $invest->update(['sattled' => true]);
                }

                $investor = Investor::findOrFail($request->investor_id);
                if ($investor->coa) {
                    $cash_head = Coa::findOrFail($request->coa_id);
                    $headCode = collect([
                        '0' => $cash_head->head_code,
                        '1' => $investor->coa->head_code,
                    ]);

                    $debit_amount = collect([
                        '0' => 0.00,
                        '1' => $data->payment,
                    ]);

                    $credit_amount = collect([
                        '0' => $data->payment,
                        '1' => 0.00,
                    ]);

                    $postData = [];
                    $countHead = count($headCode);
                    for ($i = 0; $i < $countHead; $i++) {
                        $coa = Coa::where('head_code', $headCode[$i])->first();
                        $postData[] = [
                            'voucher_no' => $data->sattlement_no,
                            'voucher_type' => "Invest Sattlement",
                            'date' => date('Y-m-d', strtotime($request->date)),
                            'coa_id' => $coa->id,
                            'coa_head_code' => $headCode[$i],
                            'narration' => 'Invest Sattlement against payment no - ' . $data->sattlement_no,
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
        $data = $this->model::findOrFail($id);
        $title = 'View ' . $this->title;
        return view("admin.{$this->path}.view", compact('title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        if ($request->ajax()) {
            $data = $this->model::findOrFail($id);
            $invests = Invest::where('investor_id', $request->investor_id)
                ->where(function ($query) use ($data, $request) {
                    $query->where('sattled', false);
                    if ($data->investor_id == $request->investor_id) {
                        $query->orWhereIn('id', $data->list->pluck('invest_id')->toArray());
                    }
                })
                ->get();
            return response()->json([
                'status' => 'success',
                'data' => view('admin.invest-sattlement.partial.edit-rows', compact('invests', 'data'))->render()
            ]);
        }

        $data = $this->model::findOrFail($id);
        $invests = Invest::where('investor_id', $data->investor_id)
            ->where(function ($query) use ($data) {
                $query->where('sattled', false)
                    ->orWhereIn('id', $data->list->pluck('invest_id')->toArray());
            })
            ->get();
        $additionalData = [
            'investors' => Investor::where('status', true)->orderBy('name', 'asc')->get(),
            'cashHeads' => Coa::whereHas('parent', function ($query) {
                $query->whereIn('head_name', ['Cash In Hand', 'Cash at Bank']);
            })->get(),
            'invests' => $invests,
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
            'coa_id' => 'required',
            'investor_id' => 'required',
            'invest_id' => 'required'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $data = $this->model::findOrFail($id);
                foreach ($data->list as $item) {
                    $invest = Invest::find($item->invest_id);
                    $invest->update(['sattled' => false]);
                    $item->delete();
                }
                AccountTransactionAuto::where('voucher_no', $data->sattlement_no)->where('voucher_type', 'Invest Sattlement')->forceDelete();

                $investQty = 0;
                $investAmount = 0;
                foreach ($request->invest_id as $invest_id) {
                    $invest = Invest::find($invest_id);
                    $investQty += $invest->qty;
                    $investAmount += $invest->amount;
                }

                $data->update([
                    'investor_id' => $request->investor_id,
                    'coa_id' => $request->coa_id,
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'invest_qty' => $investQty,
                    'invest_amount' => $investAmount,
                    'payment' => $investAmount,
                    'remarks' => $request->remarks,
                    'updated_by' => Auth::id()
                ]);

                foreach ($request->invest_id as $invest_id) {
                    $invest = Invest::find($invest_id);
                    InvestSattlementList::create([
                        'invest_sattlement_id' => $data->id,
                        'investor_id' => $invest->investor_id,
                        'invest_id' => $invest_id,
                        'product_id' => $invest->product_id,
                        'invest_qty' => $invest->qty,
                        'invest_amount' => $invest->amount,
                        'payment' => $invest->amount
                    ]);
                    $invest->update(['sattled' => true]);
                }

                $investor = Investor::findOrFail($request->investor_id);
                if ($investor->coa) {
                    $cash_head = Coa::findOrFail($request->coa_id);
                    $headCode = collect([
                        '0' => $cash_head->head_code,
                        '1' => $investor->coa->head_code,
                    ]);

                    $debit_amount = collect([
                        '0' => 0.00,
                        '1' => $data->payment,
                    ]);

                    $credit_amount = collect([
                        '0' => $data->payment,
                        '1' => 0.00,
                    ]);

                    $postData = [];
                    $countHead = count($headCode);
                    for ($i = 0; $i < $countHead; $i++) {
                        $coa = Coa::where('head_code', $headCode[$i])->first();
                        $postData[] = [
                            'voucher_no' => $data->sattlement_no,
                            'voucher_type' => "Invest Sattlement",
                            'date' => date('Y-m-d', strtotime($request->date)),
                            'coa_id' => $coa->id,
                            'coa_head_code' => $headCode[$i],
                            'narration' => 'Invest Sattlement against payment no - ' . $data->sattlement_no,
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
        // Recovery Deleted Data
        if (request()->has('recovery') && request('recovery') == 'true') {
            try {
                DB::transaction(function () use ($id) {
                    $data = $this->model::onlyTrashed()->findOrFail($id);

                    foreach ($data->list as $item) {
                        $invest = Invest::find($item->invest_id);
                        if ($invest->sattled) {
                            throw new Exception('Something went wrong!');
                        }
                        $invest->update(['sattled' => true]);
                    }

                    AccountTransactionAuto::where('voucher_no', $data->sattlement_no)->where('voucher_type', 'Invest Sattlement')->restore();
                    $data->restore();
                });
                return response()->json(['status' => 'success', 'message' => 'Successfully Recovered!']);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }

        // Delete Single Item Permanent
        if (request()->has('parmanent') && request('parmanent') == 'true') {
            try {
                DB::transaction(function () use ($id) {
                    $ids = request()->has('id') ? request('id') : [$id];
                    foreach ((array) $ids as $deleteId) {
                        $data = $this->model::onlyTrashed()->findOrFail($deleteId);
                        AccountTransactionAuto::where('voucher_no', $data->sattlement_no)->where('voucher_type', 'Invest Sattlement')->forceDelete();
                        $data->forceDelete();
                    }
                });
                return response()->json(['status' => 'success', 'message' => 'Permanently Deleted!']);;
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }

        // Delete Multiple Item
        try {
            DB::transaction(function () use ($id) {
                $ids = request()->has('id') ? request('id') : [$id];
                foreach ((array) $ids as $deleteId) {
                    $data = $this->model::findOrFail($deleteId);

                    foreach ($data->list as $item) {
                        $invest = Invest::find($item->invest_id);
                        $invest->update(['sattled' => false]);
                    }

                    AccountTransactionAuto::where('voucher_no', $data->sattlement_no)->where('voucher_type', 'Invest Sattlement')->update(['deleted_by' => Auth::id()]);
                    AccountTransactionAuto::where('voucher_no', $data->sattlement_no)->where('voucher_type', 'Invest Sattlement')->delete();

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
