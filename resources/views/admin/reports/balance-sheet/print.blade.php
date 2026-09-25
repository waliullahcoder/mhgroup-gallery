@extends('layouts.admin.print_app')
@push('css')
    <style>
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -10px;
            margin-left: -10px;
        }

        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            position: relative;
            width: 48%;
            min-height: 1px;
            padding-right: 10px;
            padding-left: 10px;
            float: left;
            margin-top: 12px;
        }

        .col-12 {
            flex: 0 0 100%;
            max-width: 100%;
            position: relative;
            width: 100%;
            min-height: 1px;
            padding-right: 10px;
            padding-left: 10px;
            margin-top: 12px;
        }

        .text-white {
            color: #fff !important;
        }

        .bg-light {
            background-color: #e9e9e9;
        }
    </style>
@endpush
@section('content')
    <div class="row g-3">
        <div class="col-md-6">
            <table class="table table-bordered mb-0 table-sm">
                <thead>
                    <tr>
                        <th>ASSETS</th>
                        <td></td>
                        <th class="text-right">AMOUNT</th>
                    </tr>
                </thead>
                @php
                    $cAsset = 0;
                    $fAsset = 0;
                    $totalL = 0;
                @endphp
                <tbody>
                    @foreach ($data['assets'] as $asset)
                        <tr class="bg-light">
                            <th><u>{{ $asset['head'] }}</u></th>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach ($asset['childs'] as $key => $child)
                            <tr>
                                <td>{{ $child['name'] }}</td>
                                <td class="text-center" width="30">=></td>
                                <td class="text-right">
                                    {{ $child['amount'] >= 0 ? number_format($child['amount'], 2) : '(' . number_format(abs($child['amount']), 2) . ')' }}
                                </td>
                            </tr>
                            @php
                                if ($asset['head'] == 'Current Asset') {
                                    $cAsset += $child['amount'];
                                } elseif ($asset['head'] == 'Fixed Asset') {
                                    $fAsset += $child['amount'];
                                }
                            @endphp
                        @endforeach
                        @if ($asset['head'] == 'Current Asset')
                            <tr class="bg-primary">
                                <td colspan="2">Total Current Asset</td>
                                <td class="text-right">
                                    {{ $cAsset >= 0 ? number_format($cAsset, 2) : '(' . number_format(abs($cAsset), 2) . ')' }}
                                </td>
                            </tr>
                        @elseif ($asset['head'] == 'Fixed Asset')
                            <tr class="bg-primary">
                                <th colspan="2">Total Fixed Asset</th>
                                <th class="text-right">
                                    {{ $fAsset >= 0 ? number_format($fAsset, 2) : '(' . number_format(abs($fAsset), 2) . ')' }}
                                </th>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-bordered mb-0 table-sm">
                <thead>
                    <tr>
                        <th>LIABILITIES</th>
                        <td></td>
                        <th class="text-right">AMOUNT</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-light">
                        <th><u>Current Liabilities</u></th>
                        <td></td>
                        <td></td>
                    </tr>
                    @foreach ($data['liabilities'] as $liability)
                        @php
                            if ($liability['head'] == 'Current Liabilities') {
                                continue;
                            }
                            $totalChildL = 0;
                        @endphp
                        @foreach ($liability['childs'] as $child)
                            @php
                                $totalChildL += $child['amount'];
                            @endphp
                        @endforeach
                        @php
                            $totalL += $totalChildL;
                        @endphp
                        <tr>
                            <td>{{ $liability['head'] }}</td>
                            <td class="text-center" width="30">=></td>
                            <td class="text-right">
                                {{ $totalChildL >= 0 ? number_format($totalChildL, 2) : '(' . number_format(abs($totalChildL), 2) . ')' }}
                            </td>
                        </tr>
                    @endforeach
                    <tr class="bg-primary">
                        <th>Total Liabilities</th>
                        <td>=></td>
                        <th class="text-right">
                            {{ $totalL >= 0 ? number_format($totalL, 2) : '(' . number_format(abs($totalL), 2) . ')' }}
                        </th>
                    </tr>
                    <tr class="bg-light">
                        <th><u>Profit & Loss:</u></th>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="bg-primary">
                        <th>Profit & Loss Balance</th>
                        <td>=></td>
                        <td class="text-right">
                            {{ $data['currentPFL'] >= 0 ? number_format($data['currentPFL'], 2) : '(' . number_format(abs($data['currentPFL']), 2) . ')' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row g-3 mt-3">
        <div class="col-md-6">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th colspan="2">Total Assets</th>
                        <th class="text-right">
                            {{ number_format($cAsset + $fAsset, 2) }}
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th colspan="2">Total Liabilities</th>
                        <th class="text-right">
                            {{ number_format($totalL + $data['currentPFL'], 2) }}
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div style="padding-top: 10px;">Print Date & Time : {{ date('d-m-Y h:i:s A') }}</div>
@endsection
