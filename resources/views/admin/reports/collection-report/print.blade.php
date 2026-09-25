@extends('layouts.admin.print_app')

@section('content')
    @if ($report_type == 'vendor_summary')
        <table class="table table-bordered table-condensed table-striped align-middle mb-3">
            <thead class="text-nowrap">
                <tr>
                    <th class="text-center" width="20">SL#</th>
                    <th>Vendor</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ @$row->vendor->name }}</td>
                        <td class="text-right">{{ number_format($row->sum_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="text-right">Total</th>
                    <th class="text-right">{{ number_format($data->sum('sum_amount'), 2) }}</th>
                </tr>
            </tfoot>
        </table>
    @else
        <table class="table table-bordered table-condensed table-striped align-middle mb-3">
            <thead class="text-nowrap">
                <tr>
                    <th class="text-center" width="20">SL#</th>
                    <th>Payment No</th>
                    <th>Date</th>
                    <th>Vendor</th>
                    <th>Pay Mode</th>
                    <th>Remarks</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ @$row->payment_no }}</td>
                        <td class="text-nowrap">{{ @$row->formattedDate }}</td>
                        <td>{{ @$row->vendor->name }}</td>
                        <td>{{ @$row->type }}</td>
                        <td>{{ @$row->remarks }}</td>
                        <td class="text-right">{{ number_format($row->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" class="text-right">Total</th>
                    <th class="text-right">{{ number_format($data->sum('amount'), 2) }}</th>
                </tr>
            </tfoot>
        </table>
    @endif
    <div style="padding-top: 30px;">Print Date & Time : {{ date('d-m-Y h:i:s A') }}</div>
@endsection
