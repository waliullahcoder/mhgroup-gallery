<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Coa;
use App\HelperClass;
use App\Models\Invest;
use App\Models\Product;
use App\Models\Investor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\AccountTransactionAuto;

class InvestController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'invest';
        $this->title = 'Invest Process';
        $this->create_title = 'Add invest';
        $this->edit_title = 'Update invest';
        $this->model = Invest::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = $this->model::with(['investor', 'product'])->orderBy('id', 'desc');

            if (request()->has('customFilter')) {
                if (request('customFilter') == 'Closed') {
                    $query->where('sattled', true);
                } else {
                    $query->where('sattled', false);
                }
            }

            $sumQuery = (clone $query);
            $type = request('type');
            if (!empty($type) && $type === 'trash') {
                $sumQuery->onlyTrashed();
            }

            if (request()->filled('search.value')) {
                $search = request()->input('search.value');

                $sumQuery->where(function ($q) use ($search) {
                    // Search in project.name relation
                    $q->orWhereHas('product', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });

                    // Search in investor.name relation
                    $q->orWhereHas('investor', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });

                    // Search in payment_type column
                    $q->orWhere('invest_no', 'like', "%{$search}%");

                    // Search in qty column (numeric, so cast to string for like search)
                    $q->orWhere('qty', 'like', "%{$search}%");

                    // Search in amount column (numeric, cast to string for like search)
                    $q->orWhere('amount', 'like', "%{$search}%");
                });
            }

            $totalQty = $sumQuery->sum('qty');
            $totalAmount = $sumQuery->sum('amount');

            $addition_btns = [[
                'parameter' => true,
                'target' => '_self',
                'title' => 'View Invest',
                'route' => "admin.{$this->path}.show",
                'icon' => '<i class="fas fa-eye"></i>',
                'class' => 'btn btn-sm btn-primary mw-fit',
            ]];

            // Get JSON from HelperClass
            $response = HelperClass::resourceDataView($query, null, $addition_btns, $this->path, $this->title, ['payments', 'sattlements', 'transactions'], 'conditional');

            // Decode, merge totals, then return
            $data = $response->getData(true);
            $data['totalQty'] = $totalQty;
            $data['totalAmount'] = $totalAmount;

            return response()->json($data);
        }

        return view("admin.{$this->path}.index", ['title' => $this->title]);
    }

    public function investNo()
    {
        $prefix = 'I' . date('ym'); // I + YYMM, e.g. I2506

        $data = $this->model::withTrashed()
            ->select(['invest_no'])
            ->where('invest_no', 'like', $prefix . '%') // filter current month
            ->orderBy('id', 'desc')
            ->first();

        if (is_null($data)) {
            $serial = '001';
        } else {
            $lastNumber = (int)substr($data->invest_no, -3); // last 3 digits
            $serial = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }

        return $prefix . $serial;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $title = $this->create_title;
        $invest_no = $this->investNo();
        $investors = Investor::where('status', true)->orderBy('name', 'asc')->get();
        $cashHeads = Coa::with('parent')->whereHas('parent', function ($query) {
            $query->whereIn('head_name', ['Cash In Hand', 'Cash at Bank']);
        })->get();
        $products = Product::where('status', true)->orderBy('id', 'desc')->get();
        return view("admin.{$this->path}.create", compact('title', 'invest_no', 'investors', 'cashHeads', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'qty' => 'required',
            'date' => 'required',
            'amount' => 'required',
            'investor_id' => 'required'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $data = $this->model::create([
                    'investor_id' => $request->investor_id,
                    'product_id' => $request->product_id,
                    'invest_no' => $this->investNo(),
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'qty' => $request->qty,
                    'amount' => $request->amount,
                    'coa_id' => $request->coa_id,
                    'approved' => true,
                    'created_by' => Auth::id(),
                ]);

                $investor = Investor::findOrFail($request->investor_id);
                if ($investor->coa) {
                    $cash_head = Coa::findOrFail($request->coa_id);
                    $headCode = collect([
                        '0' => $cash_head->head_code,
                        '1' => $investor->coa->head_code
                    ]);

                    $debit_amount = collect([
                        '0' => $request->amount,
                        '1' => 0.00
                    ]);

                    $credit_amount = collect([
                        '0' => 0.00,
                        '1' => $request->amount,
                    ]);

                    $postData = [];
                    $countHead = count($headCode);
                    for ($i = 0; $i < $countHead; $i++) {
                        $coa = Coa::where('head_code', $headCode[$i])->first();
                        $postData[] = [
                            'voucher_no' => $data->invest_no,
                            'voucher_type' => "Invest",
                            'date' => date('Y-m-d', strtotime($request->date)),
                            'coa_id' => $coa->id,
                            'coa_head_code' => $headCode[$i],
                            'narration' => 'Invest against invest no - ' . $data->invest_no,
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
        $additionalData = [
            'investors' => Investor::where('status', true)->orderBy('name', 'asc')->get(),
            'cashHeads' => Coa::with('parent')->whereHas('parent', function ($query) {
                $query->whereIn('head_name', ['Cash In Hand', 'Cash at Bank']);
            })->get(),
            'products' => Product::where('status', true)->orderBy('id', 'desc')->get()
        ];
        return HelperClass::resourceDataEdit($this->model, $id, $this->path, $this->edit_title, $additionalData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'qty' => 'required',
            'date' => 'required',
            'amount' => 'required',
            'investor_id' => 'required'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $data = $this->model::findOrFail($id);
                $data->update([
                    'investor_id' => $request->investor_id,
                    'product_id' => $request->product_id,
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'qty' => $request->qty,
                    'amount' => $request->amount,
                    'coa_id' => $request->coa_id,
                    'updated_by' => Auth::id(),
                ]);

                AccountTransactionAuto::where('voucher_no', $data->invest_no)->where('voucher_type', 'Invest')->forceDelete();

                $investor = Investor::findOrFail($request->investor_id);
                if ($investor->coa) {
                    $cash_head = Coa::findOrFail($request->coa_id);
                    $headCode = collect([
                        '0' => $cash_head->head_code,
                        '1' => $investor->coa->head_code
                    ]);

                    $debit_amount = collect([
                        '0' => $request->amount,
                        '1' => 0.00
                    ]);

                    $credit_amount = collect([
                        '0' => 0.00,
                        '1' => $request->amount,
                    ]);

                    $postData = [];
                    $countHead = count($headCode);
                    for ($i = 0; $i < $countHead; $i++) {
                        $coa = Coa::where('head_code', $headCode[$i])->first();
                        $postData[] = [
                            'voucher_no' => $data->invest_no,
                            'voucher_type' => "Invest",
                            'date' => date('Y-m-d', strtotime($request->date)),
                            'coa_id' => $coa->id,
                            'coa_head_code' => $headCode[$i],
                            'narration' => 'Invest against invest no - ' . $data->invest_no,
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
                    AccountTransactionAuto::where('voucher_no', $data->invest_no)->where('voucher_type', 'Invest')->restore();
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
                        AccountTransactionAuto::where('voucher_no', $data->invest_no)->where('voucher_type', 'Invest')->forceDelete();
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

                    AccountTransactionAuto::where('voucher_no', $data->invest_no)->where('voucher_type', 'Invest')->update(['deleted_by' => Auth::id()]);
                    AccountTransactionAuto::where('voucher_no', $data->invest_no)->where('voucher_type', 'Invest')->delete();

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
