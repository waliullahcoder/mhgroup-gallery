<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\Invest;
use App\Models\Product;
use App\Models\SalesList;
use Illuminate\Http\Request;
use App\Models\ProductionList;
use App\Models\ProfitDistribution;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\ProfitDistributionList;

class ProfitDistributionController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path         = 'profit-distribution';
        $this->title        = 'Profit Distribution';
        $this->create_title = 'Add Distribution';
        $this->edit_title   = 'Update Distribution';
        $this->model        = ProfitDistribution::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addition_btns = [[
            'parameter' => true,
            'target'    => '_self',
            'title'     => 'View Distribution',
            'route'     => "admin.{$this->path}.show",
            'icon'      => '<i class="fas fa-eye"></i>',
            'class'     => 'btn btn-sm btn-primary mw-fit',
        ]];
        return HelperClass::resourceDataView($this->model::orderBy('id', 'desc'), null, $addition_btns, $this->path, $this->title, ['paidList'], 'conditional');
    }

    public function serialNo()
    {
        $prefix = 'PD' . date('ym'); // I + YYMM, e.g. I2506

        $data = $this->model::withTrashed()
            ->select(['serial_no'])
            ->where('serial_no', 'like', $prefix . '%') // filter current month
            ->orderBy('id', 'desc')
            ->first();

        if (is_null($data)) {
            $serial = '001';
        } else {
            $lastNumber = (int)substr($data->serial_no, -3); // last 3 digits
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
            $detailData = [];
            $checkData = $this->model::where('year', $request->year)->where('month', $request->month)->where('product_id', $request->product_id)->first();
            if (is_null($checkData)) {
                $startDate = date("Y-m-01", strtotime("$request->year-$request->month-01"));
                $endDate = date("Y-m-t", strtotime("$request->year-$request->month-01"));
                $sales = SalesList::with(['product.invests' => function ($q) {
                        $q->where('sattled', false);
                    }])
                    ->whereHas('product', function ($query) use ($endDate) {
                        $query->whereHas('invests', function ($subQuery) use ($endDate) {
                            $subQuery->where('date', '<=', $endDate)->where('sattled', false);
                        });
                    })
                    ->whereHas('sales', function ($query) use ($endDate) {
                        $query->where('date', '<=', $endDate);
                    })
                    ->where('product_id', $request->product_id)
                    ->where('distributed', false)
                    ->select('product_id', DB::raw('SUM(qty - return_qty) as sumQty'), DB::raw('SUM(net_amount - return_amount) as sumAmount'))
                    ->groupBy('product_id')
                    ->get();

                $invests = Invest::with(['investor', 'product'])->where('product_id', $request->product_id)->where('date', '<=', $endDate)->where('sattled', false)->get();
                $product = Product::findOrFail($request->product_id);
                // $profit_percent=$product->profit_percent/100;
                // $profitAmount = ($sales->sum('sumQty') * $profit_percent) * $product->profit;
                $profitAmount = $invests->sum('qty')*$product->profit;

                $productionQty = ProductionList::whereHas('production', function ($query) use ($endDate) {
                        $query->where('date', '<=', $endDate);
                    })->where('product_id', $request->product_id)->sum('qty');
                $detailData = [
                    'invests' => $invests,
                    'product' => $product,
                    'production_qty' => $productionQty,
                    'sales_qty' => $sales->sum('sumQty'),
                    'sales_amount' => $sales->sum('sumAmount'),
                    'invest_qty' => $invests->sum('qty'),
                    'invest_amount' => $invests->sum('amount'),
                    'profit_amount' => $profitAmount,
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ];

                return response()->json([
                    'status' => 'success',
                    'data' => view('admin.profit-distribution.partial.data', ['detailData' => $detailData])->render()
                ]);
            }
        }

        $title = $this->create_title;
        $serial_no = $this->serialNo();
        $detailData = [];
        $checkData = $this->model::where('year', date('Y'))->where('month', date('F'))->first();
        return view("admin.{$this->path}.create", compact('title', 'serial_no', 'detailData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required',
            'month' => 'required',
            'date' => 'required',
            'invest_id' => 'required'
        ]);
        
        try {
            DB::transaction(function () use ($request) {
                $data = $this->model::create([
                    'serial_no' => $this->serialNo(),
                    'year'      => $request->year,
                    'month'     => $request->month,
                    'date'      => date('Y-m-d', strtotime($request->date)),
                    'product_id'    => $request->product_id,
                    'invest_qty'    => $request->invest_qty,
                    'invest_amount' => $request->invest_amount,
                    'profit_amount' => $request->profit_amount,
                    'production_qty' => $request->production_qty, 
                    'sales_qty'     => $request->sales_qty,
                    'sales_amount'  => $request->sales_amount,
                    'created_by'    => Auth::id(),
                ]);

                $startDate = date("Y-m-01", strtotime("$request->year-$request->month-01"));
                $endDate = date("Y-m-t", strtotime("$request->year-$request->month-01"));
                $totalInvestQty = Invest::with(['investor', 'product'])->where('date', '<=', $endDate)->where('product_id', $request->product_id)->where('sattled', false)->sum('qty');
                foreach ($request->invest_id as $invest_id) {
                    $invest = Invest::find($invest_id);
                    $perShareProfit = $request->profit_amount / $totalInvestQty;

                    ProfitDistributionList::create([
                        'profit_distribution_id' => $data->id,
                        'invest_id'     => $invest_id,
                        'investor_id'   => $invest->investor_id,
                        'product_id'    => $invest->product_id,
                        'profit_per_sale' => $invest->product->profit,
                        'sales_qty'     => $request->sales_qty,
                        'invest_qty'    => $invest->qty,
                        'invest_amount' => $invest->amount,
                        'amount'        => $perShareProfit * $invest->qty
                    ]);
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
        $title = 'View ' . $this->title;
        $data = $this->model::findOrFail($id);
        return view("admin.{$this->path}.view", compact('data', 'title'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $detailData = [];
        $data = $this->model::findOrFail($id);
        $checkData = $this->model::whereNot('id', $id)->where('year', $request->year)->where('month', $request->month)->where('product_id', $request->product_id)->first();
        if (is_null($checkData)) {
            $startDate = date("Y-m-01", strtotime("$request->year-$request->month-01"));
            $endDate = date("Y-m-t", strtotime("$request->year-$request->month-01"));
            $sales = SalesList::with(['product.invests' => function ($q) {
                $q->where('sattled', false);
            }])
                ->whereHas('product', function ($query) use ($endDate) {
                    $query->whereHas('invests', function ($subQuery) use ($endDate) {
                        $subQuery->where('date', '<=', $endDate)->where('sattled', false);
                    });
                })
                ->whereHas('sales', function ($query) use ($endDate) {
                    $query->where('date', '<=', $endDate);
                })
                ->where(function ($query) use ($data, $request) {
                    $query->where('distributed', false);
                })
                ->where('product_id', $request->product_id)
                ->groupBy('product_id')
                ->select('product_id', DB::raw('SUM(qty) as sumQty'))
                ->get();

            $invests = Invest::with(['investor', 'product'])->where('date', '<=', $endDate)->where('sattled', false)->get();
            $profitAmount = $sales->sum(function ($item) {
                return $item->product->profit * $item->sumQty;
            });
            
            $product = Product::findOrFail($request->product_id);

            $detailData = [
                'invests' => $invests,
                'product' => $product,
                'sales_qty' => round(($sales->sum('qty') - $sales->sum('return_qty')) * 0.9),
                'invest_qty' => $invests->sum('qty'),
                'invest_amount' => $invests->sum('amount'),
                'profit_amount' => $profitAmount,
                'startDate' => $startDate,
                'endDate' => $endDate
            ];
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => view('admin.profit-distribution.partial.data', ['detailData' => $detailData])->render()
            ]);
        }

        $additionalData = [
            'detailData' => $detailData,
        ];
        return HelperClass::resourceDataEdit($this->model, $id, $this->path, $this->edit_title, $additionalData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'year' => 'required',
            'month' => 'required',
            'date' => 'required',
            'invest_id' => 'required'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $data = $this->model::findOrFail($id);
                $data->update([
                    'year'  => $request->year,
                    'month' => $request->month,
                    'date'  => date('Y-m-d', strtotime($request->date)),
                    'invest_qty' => $request->invest_qty,
                    'invest_amount' => $request->invest_amount,
                    'profit_amount' => $request->profit_amount,
                    'production_qty' => $request->production_qty, 
                    'sales_qty'     => $request->sales_qty,
                    'sales_amount'  => $request->sales_amount,
                    'updated_by'    => Auth::id(),
                ]);

                ProfitDistributionList::where('profit_distribution_id', $id)->delete();

                $startDate = date("Y-m-01", strtotime("$request->year-$request->month-01"));
                $endDate = date("Y-m-t", strtotime("$request->year-$request->month-01"));
                $invests = Invest::with(['investor', 'product'])->where('date', '<=', $endDate)->where('sattled', false)->get();
                foreach ($request->invest_id as $invest_id) {
                    $invest = Invest::find($invest_id);
                    $totalInvestQty = $invests->where('product_id', $invest->product_id)->where('sattled', false)->sum('qty');

                    $salesQty = SalesList::where('product_id', $invest->product_id)
                        ->whereHas('sales', function ($query) use ($startDate, $endDate) {
                            $query->whereBetween('date', [$startDate, $endDate]);
                        })
                        ->sum('qty');
                    $totalProfit = $salesQty * $invest->product->profit;
                    $perShareProfit = round($totalProfit / $totalInvestQty);

                    ProfitDistributionList::create([
                        'profit_distribution_id' => $data->id,
                        'invest_id'     => $invest_id,
                        'investor_id'   => $invest->investor_id,
                        'product_id'    => $invest->product_id,
                        'profit_per_sale' => $invest->product->profit,
                        'sales_qty'     => $request->sales_qty,
                        'invest_qty'    => $invest->qty,
                        'invest_amount' => $invest->amount,
                        'amount'        => $perShareProfit * $invest->qty
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
        return HelperClass::resourceDataDelete($this->model, $id);
    }
}
