@extends('layouts.admin.app')

@section('content')
    <div class="card">
        <div class="card-header pe-2 py-2">
            <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center">
                <h6 class="h6 mb-0 text-uppercase text-nowrap flex-grow-1">
                    {{ isset($title) ? $title : 'Please Set Title' }}</h6>
                @php
                    $route = \Request::route()->getName();
                    $indexRoute = str_replace('show', 'index', $route);
                @endphp
                <a href="{{ Route($indexRoute) }}" class="btn btn-primary btn-sm">Go Back</a>
            </div>
        </div>
        <div class="card-body px-3">
            <table class="table table-borderless table-striped table-responsive-sm">
                <tbody>
                    <tr>
                        <th width="200">Production No</th>
                        <th width="10">:</th>
                        <td>{{ $data->production_no }}</td>
                    </tr>
                    <tr>
                        <th width="200">Date</th>
                        <th width="10">:</th>
                        <td>{{ date('d-m-Y', strtotime($data->date)) }}</td>
                    </tr>
                    <tr>
                        <th width="200">Store</th>
                        <th width="10">:</th>
                        <td>{{ $data->store->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <th width="200">Total Qty</th>
                        <th width="10">:</th>
                        <td>{{ $data->total_qty }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered table-striped table-responsive-sm">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="text-center" width="20">SL#</th>
                        <th>Book Name</th>
                        <th>Edition</th>
                        <th class="text-end">Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->list as $row)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-nowrap">{{ $row->product->name ?? '' }}</td>
                            <td class="text-nowrap">{{ $row->edition->name ?? '' }}</td>
                            <td class="text-end">{{ $row->qty }} </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
