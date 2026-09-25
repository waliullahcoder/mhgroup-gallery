@extends('layouts.admin.print_app')
@section('content')
    <table class="table mb-3 info-table" style="border-bottom: 1px solid #ddd;">
        <tr>
            <th width="65">Production No</th>
            <td><b> : </b>{{ $data->production_no }}</td>
            <th class="text-right" width="70">Date :</th>
            <td class="text-left text-nowrap" width="65">{{ date('d-m-Y', strtotime($data->date)) }}</td>
        </tr>
        <tr>
            <th width="65">Store</th>
            <td><b> : </b>{{ $data->store->name ?? '' }}</td>
            <th class="text-right" width="70">Total Qty :</th>
            <td class="text-left text-nowrap" width="65">{{ $data->total_qty }}</td>
        </tr>
    </table>
    <table class="table table-bordered table-condensed table-striped align-middle mb-3">
        <thead>
            <tr>
                <th class="text-center" width="20">SL#</th>
                <th>Book Name</th>
                <th>Edition</th>
                <th class="text-right">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->list as $row)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-nowrap">{{ $row->product->name ?? '' }}</td>
                    <td class="text-nowrap">{{ $row->edition->name ?? '' }}</td>
                    <td class="text-right">{{ $row->qty }} </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding-top: 50px;">Print Date & Time : {{ date('d-m-Y h:i:s A') }}</div>
@endsection
