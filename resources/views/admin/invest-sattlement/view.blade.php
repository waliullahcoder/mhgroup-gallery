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
                            <th width="200">Investor Name</th>
                            <th width="10">:</th>
                            <td>{{ @$data->investor->name }}</td>
                        </tr>
                        <tr>
                            <th width="200">Sattlement No.</th>
                            <th width="10">:</th>
                            <td>{{ $data->sattlement_no }}</td>
                        </tr>
                        <tr>
                            <th width="200">Date</th>
                            <th width="10">:</th>
                            <td>{{ date('d-m-Y', strtotime($data->date)) }}</td>
                        </tr>
                        <tr>
                            <th width="200">Cash Head</th>
                            <th width="10">:</th>
                            <td>{{ $data->coa->head_name }} - {{ $data->coa->head_code }}</td>
                        </tr>
                        <tr>
                            <th width="200">Invest Amount</th>
                            <th width="10">:</th>
                            <td>{{ $data->invest_amount }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="pt-3">
                <table class="table">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-center" width="30">SL#</th>
                            <th>Invest No.</th>
                            <th>Invest Date</th>
                            <th class="px-1 text-end">Invest Qty</th>
                            <th class="px-1 text-end">Invest Amount</th>
                        </tr>
                    </thead>
                    <tbody id="schedules">
                        @foreach ($data->list as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $item->invest->invest_no ?? '' }}</td>
                                <td>{{ $item->invest->formattedDate ?? '' }}</td>
                                <td style="padding: 4px 0.25rem;">
                                    <input type="number" class="form-control input-sm text-end" step="any"
                                        value="{{ $item->invest->qty ?? 0 }}" placeholder="0.00" readonly>
                                </td>
                                <td style="padding: 4px 0.25rem;">
                                    <input type="number" class="form-control input-sm text-end" step="any"
                                        value="{{ $item->invest->amount ?? 0 }}" placeholder="0.00" readonly>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
