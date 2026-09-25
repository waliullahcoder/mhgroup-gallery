@extends('layouts.admin.print_app')
@section('content')
    <table class="table table-bordered table-condensed table-striped align-middle">
        <thead>
            <tr>
                <th class="text-right" colspan="5">Previous Balance</th>
                <th class="text-right">{{ number_format($previous_balance, 2) }}</th>
            </tr>
            <tr>
                <th class="text-center" width="30">Sl#</th>
                <th>Date</th>
                <th>Remarks</th>
                <th class="text-right">Amount In</th>
                <th class="text-right">Amount Out</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $balance = $previous_balance;
                $amount_in = 0;
                $amount_out = 0;
            @endphp
            @foreach ($statements as $row)
                @php
                    $balance += $row['amount_in'] - $row['amount_out'];
                    $amount_in += $row['amount_in'];
                    $amount_out += $row['amount_out'];
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ date('d-m-Y', strtotime($row['date'])) }}</td>
                    <td>{{ $row['remarks'] }}</td>
                    <td class="text-right">{{ number_format($row['amount_in'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['amount_out'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['balance'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding-top: 10px;">Print Date & Time : {{ date('d-m-Y h:i:s A') }}</div>
@endsection
