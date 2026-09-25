@extends('layouts.admin.print_app')

@section('content')
    <table class="table mb-3 info-table" style="border-bottom: 1px solid #ddd;">
        <tr>
            <th width="60">Return No</th>
            <td><b> : </b> {{ $data->return_no }}</td>
            <th class="text-right" width="70">Date : </th>
            <td class="text-left text-nowrap" width="60">{{ date('d-m-Y', strtotime($data->date)) }}</td>
        </tr>
        <tr>
            <th width="60">Client</th>
            <td><b> : </b> {{ $data->client->name ?? '' }}</td>
            <th class="text-right" width="70">Store : </th>
            <td class="text-left text-nowrap" width="60">{{ $data->store->name ?? '' }}</td>
        </tr>
        <tr>
            <th width="60">Remarks</th>
            <td colspan="3"><b> : </b> {{ @$data->remarks }}</td>
        </tr>
    </table>

    <table class="table table-bordered table-condensed table-striped align-middle mb-3">
        <thead>
            <tr class="text-nowrap">
                <th class="text-center" width="20">SL#</th>
                <th>Invoice</th>
                <th>Product</th>
                <th class="text-right">Rate</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->list as $row)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $row->sales->invoice ?? '' }}</td>
                    <td>{{ $item->product->name ?? '' }} - {{ $item->edition->name ?? '' }}</td>
                    <td class="text-right">{{ number_format($row->rate, 2) }}</td>
                    <td class="text-right">{{ $row->qty }} </td>
                    <td class="text-right">{{ number_format($row->amount, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right" colspan="5"><b>Total</b></td>
                <td class="text-right" colspan="1"><b>{{ number_format($data->amount, 2) }}</b></td>
            </tr>
        </tfoot>
    </table>
    <div class="mb-3">
        <b>In Words :</b> {{ Str::title(\App\HelperClass::convertNumber($data->amount)) }} Taka Only.
    </div>
    <div>
        <div class="signature-area">
            <div class="signature-item">
                <span>Receive By</span>
            </div>
            <div class="signature-item">
                <i class="staff">{{ $data->user->name ?? '' }}</i>
                <span>Prepare By</span>
            </div>
        </div>
    </div>
    <div style="padding-top: 50px;">Print Date & Time : {{ date('d-m-Y h:i:s A') }}</div>
@endsection
