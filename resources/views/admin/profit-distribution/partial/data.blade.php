<div class="row g-3">
    <div class="col-md-3 col-sm-6">
        <label for="sales_qty" class="form-label"><b>Total Sales</b></label>
        <input type="text" class="form-control" id="sales_qty" name="sales_qty"
            value="{{ $detailData['sales_qty'] ?? 0 }}" readonly>
    </div>
    <div class="col-md-3 col-sm-6">
        <label for="profit_amount" class="form-label"><b>Total Profit</b></label>
        <input type="text" class="form-control" id="profit_amount" name="profit_amount"
            value="{{ round($detailData['profit_amount']) ?? 0 }}" readonly>

        <input type="text" class="form-control d-none" id="production_qty" name="production_qty"
            value="{{ $detailData['production_qty'] ?? 0 }}" readonly>
        <input type="text" class="form-control d-none" id="sales_amount" name="sales_amount"
            value="{{ $detailData['sales_amount'] ?? 0 }}" readonly>
    </div>
    <div class="col-md-3 col-sm-6">
        <label for="invest_qty" class="form-label"><b>Total Invest Qty</b></label>
        <input type="text" class="form-control" id="invest_qty" name="invest_qty"
            value="{{ $detailData['invest_qty'] ?? 0 }}" readonly>
    </div>
    <div class="col-md-3 col-sm-6">
        <label for="invest_amount" class="form-label"><b>Total Invest Amount</b></label>
        <input type="text" class="form-control" id="invest_amount" name="invest_amount"
            value="{{ $detailData['invest_amount'] ?? 0 }}" readonly>
    </div>
    <div class="col-12">
        <div class="table-responsive">
            <table class="table table-striped text-nowrap mb-0">
                <thead>
                    <tr class="bg-primary text-white">
                        <th class="text-center" width="30">SL#</th>
                        <th>Investor</th>
                        <th>Product</th>
                        <th class="text-end">Invest Amount</th>
                        <th class="text-end">Sales Qty</th>
                        <th class="text-end">Total Profit</th>
                        <th class="text-end">Individual Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($detailData['invests']))
                        @foreach ($detailData['invests'] as $item)
                            @php
                                $totalInvestQty = $detailData['invests']
                                    ->where('product_id', $item->product_id)
                                    // ->where('sattled', false)
                                    ->sum('qty');
                                $salesQty = \App\Models\SalesList::where('product_id', $item->product_id)
                                    ->whereHas('sales', function ($query) use ($detailData) {
                                        $query->where('date', '<=', $detailData['endDate']);
                                    })
                                    ->where('distributed', false)
                                    ->sum(DB::raw('qty - return_qty'));
                                //$profit_percent=$item->product->profit_percent/100;  
                               // $totalProfit = $salesQty * $item->product->profit;
                                // $perShareProfit = $totalProfit / $item->product->required_share;
                                 $totalProfit = $totalInvestQty * $item->product->profit;
                                $perShareProfit = $totalProfit / $totalInvestQty;
                            @endphp
                            <tr>
                                <input type="hidden" name="invest_id[]" value="{{ $item->id }}">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $item->investor->name ?? '' }}</td>
                                <td>{{ $item->product->name ?? '' }}</td>
                                <td><input type="number" class="form-control input-sm text-end" step="any"
                                        value="{{ $item->amount }}" readonly></td>
                                <td><input type="number" class="form-control input-sm text-end" step="any"
                                        value="{{ $salesQty }}" readonly></td>
                                <td><input type="number" class="form-control input-sm text-end" step="any"
                                        value="{{ round($totalProfit,2) }}" readonly></td>
                                <td><input type="number" class="form-control input-sm text-end" step="any"
                                        value="{{ round($perShareProfit,2) }}" readonly></td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
