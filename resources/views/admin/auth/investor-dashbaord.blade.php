@extends('layouts.admin.app')

@section('content')
    <div class="row g-3 info-cards">
        @php
            $totalInvest = \App\Models\Invest::where('sattled', false)
                ->where('investor_id', Auth::user()->investor->id)
                ->sum('amount');
            $totalWithdraw = \App\Models\Payment::whereIn('payment_type', ['Payment', 'Advance'])
                ->where('investor_id', Auth::user()->investor->id)
                ->sum('amount');
            $totalDue = \App\Models\ProfitDistributionList::whereHas('profitDistribution')
                ->where('investor_id', Auth::user()->investor->id)
                ->sum(DB::raw('amount - paid_amount'));
        @endphp
        <div class="col-lg-4 col-sm-6">
            <div class="card info-card">
                <div class="card-body">
                    <div class="card-content">
                        <p class="card-text">Total Invest</p>
                        <h3 class="card-count">{{ number_format($totalInvest) }}</h3>
                    </div>
                    <div class="card-icon">
                        <span class="material-symbols-outlined"> credit_score </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card info-card">
                <div class="card-body">
                    <div class="card-content">
                        <p class="card-text">Total Withdraw</p>
                        <h3 class="card-count">{{ number_format($totalWithdraw) }}</h3>
                    </div>
                    <div class="card-icon">
                        <span class="material-symbols-outlined"> checkbook </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card info-card">
                <div class="card-body">
                    <div class="card-content">
                        <p class="card-text">Total Due</p>
                        <h3 class="card-count">{{ number_format($totalDue) }}</h3>
                    </div>
                    <div class="card-icon">
                        <span class="material-symbols-outlined"> calendar_clock </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="row g-3">
                @foreach($data as $item)
                    <div class="col-md-4 col-sm-6">
                        <ul class="list-group">
                            @php
                                $bg = 'active';
                                if($item['sattled_qty'] == $item['share_qty']){
                                    $bg = 'bg-success';
                                } elseif($item['sattled_qty'] > 0){
                                    $bg = 'bg-dark text-white';
                                } elseif(($item['product']->required_share ?? 0) - ($item['share_qty'] ?? 0) <= 0) {
                                    $bg = 'bg-danger text-white';
                                }
                            @endphp
                            <li class="list-group-item {{ $bg }} text-center">
                                <i class="fas fa-book"></i>
                                <b>{{ $item['product']->name ?? '' }}</b>
                            </li>
                            <li class="list-group-item d-flex bg-light" title="Books Sales Qty">
                                <span class="d-inline-block" style="min-width: 135px;">Books Sales Qty</span> =&gt;
                                &nbsp;&nbsp;&nbsp;
                                <div class="flex-grow-1 text-end">
                                    {{ $item['sales_qty'] ?? 0 }}
                                </div>
                            </li>
                            <li class="list-group-item d-flex" title="Sales Amount">
                                <span class="d-inline-block" style="min-width: 135px;">Sales Amount</span> =&gt;
                                &nbsp;&nbsp;&nbsp;
                                <div class="flex-grow-1 text-end">
                                    {{ $item['sales_amount'] ?? 0 }}
                                </div>
                            </li>
                            <li class="list-group-item d-flex bg-light" title="Investment Required">                            
                                <span class="d-inline-block" style="min-width: 135px;">Total Investment Amount</span> =&gt;
                                &nbsp;&nbsp;&nbsp;
                                <div class="flex-grow-1 text-end">
                                    {{ ($item['product']->required_share ?? 0) * ($admin_setting->invest_value ?? 0)  }}
                                </div>
                            </li>
                            <li class="list-group-item d-flex" title="Share Qty">                            
                                <span class="d-inline-block" style="min-width: 135px;">Total Share Qty</span> =&gt;
                                &nbsp;&nbsp;&nbsp;
                                <div class="flex-grow-1 text-end">
                                    {{ $item['product']->required_share ?? 0 }}
                                </div>
                            </li>
                            <li class="list-group-item bg-light" title="Invested Share">  
                                <a href="{{ request()->fullUrl() }}?product_id={{ $item['product']->id }}&get_investors=true"
                                    class="d-flex investorBtn">                          
                                    <span class="d-inline-block" style="min-width: 135px;">Your Invested Share</span> =&gt;
                                    &nbsp;&nbsp;&nbsp;
                                    <div class="flex-grow-1 text-end">
                                        {{ $item['share_qty'] ?? 0 }}
                                    </div>
                                </a>
                            </li>
                            <li class="list-group-item bg-light" title="Invested Share">  
                                <a href="{{ request()->fullUrl() }}?product_id={{ $item['product']->id }}&get_investors=true"
                                    class="d-flex investorBtn">                          
                                    <span class="d-inline-block" style="min-width: 135px;">Your Invested Amount</span> =&gt;
                                    &nbsp;&nbsp;&nbsp;
                                    <div class="flex-grow-1 text-end">
                                        {{ $item['share_qty']*$admin_setting->invest_value ?? 0 }}
                                    </div>
                                </a>
                            </li>
                            <li class="list-group-item d-flex {{ ($item['product']->required_share ?? 0) - ($item['share_qty'] ?? 0) > 0 ? 'text-success' : 'text-danger' }}" title="Available Share">                            
                                <span class="d-inline-block" style="min-width: 135px;">Available Share</span> =&gt;
                                &nbsp;&nbsp;&nbsp;
                                <div class="flex-grow-1 text-end">
                                    {{ ($item['product']->required_share ?? 0) - ($item['share_qty'] ?? 0) }}
                                </div>
                            </li>
                            <li class="list-group-item d-flex bg-light" title="Investor Profit">                            
                                <span class="d-inline-block" style="min-width: 135px;">Investor Profit</span> =&gt;
                                &nbsp;&nbsp;&nbsp;
                                <div class="flex-grow-1 text-end">
                                    {{ $item['investor_profit'] ?? 0 }}
                                </div>
                            </li>
                            <li class="list-group-item d-flex" title="Per Share Profit">
                                <span class="d-inline-block" style="min-width: 135px;">Per Share Profit</span> =&gt;
                                &nbsp;&nbsp;&nbsp;
                                <div class="flex-grow-1 text-end">
                                    {{ round($item['per_share_profit'],2) ?? 0 }}
                                </div>
                            </li>
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('click', '.investorBtn', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                $('#response').html('');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _method: 'GET'
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#response').html(response.data);
                            $('#detailsModal').modal('show');
                        }
                    }
                });
            });
        });
    </script>
@endpush
