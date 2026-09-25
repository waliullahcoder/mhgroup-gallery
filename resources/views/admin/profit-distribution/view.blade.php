@extends('layouts.admin.app')

@section('content')
    <div class="card">
        <div class="card-header pe-2 py-2">
            <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center">
                <h6 class="h6 mb-0 text-uppercase text-nowrap flex-grow-1">
                    {{ @$title ?? 'Please Set Title' }}</h6>
                <a href="{{ Route(str_replace('show', 'index', \Request::route()->getName())) }}"
                    class="btn btn-primary btn-sm">Go Back</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive-sm">
                <table class="table table-borderless table-striped mb-0">
                    <tbody>
                        <tr>
                            <th width="200">Serial No</th>
                            <th width="10">:</th>
                            <td>{{ $data->serial_no }}</td>
                        </tr>
                        <tr>
                            <th width="200">Date</th>
                            <th width="10">:</th>
                            <td>{{ $data->formattedDate }}</td>
                        </tr>
                        <tr>
                            <th width="200">Year</th>
                            <th width="10">:</th>
                            <td>{{ $data->year }}</td>
                        </tr>
                        <tr>
                            <th width="200">Month</th>
                            <th width="10">:</th>
                            <td>{{ $data->month }}</td>
                        </tr>
                        <tr>
                            <th width="200">Invest Qty</th>
                            <th width="10">:</th>
                            <td>{{ $data->invest_qty }}</td>
                        </tr>
                        <tr>
                            <th width="200">Invest Amount</th>
                            <th width="10">:</th>
                            <td>{{ $data->invest_amount }}</td>
                        </tr>
                        <tr>
                            <th width="200">Profit Amount</th>
                            <th width="10">:</th>
                            <td>{{ $data->profit_amount }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="pt-3">
                <table class="table">
                    <thead class="bg-primary text-white">
                        <tr class="bg-primary text-white">
                            <th class="text-center" width="30">SL#</th>
                            <th>Investor</th>
                            <th>Product</th>
                            <th class="text-end">Invest Amount</th>
                            <th class="text-end">Sales Qty</th>
                            <th class="text-end">Individual Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data->list as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $item->investor->name ?? '' }}</td>
                                <td>{{ $item->product->name ?? '' }}</td>
                                <td><input type="number" class="form-control input-sm text-end" step="any"
                                        value="{{ $item->invest_amount }}" readonly></td>
                                <td><input type="number" class="form-control input-sm text-end" step="any"
                                        value="{{ $item->sales_qty }}" readonly></td>
                                <td><input type="number" class="form-control input-sm text-end" step="any"
                                        value="{{ $item->amount }}" readonly></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
