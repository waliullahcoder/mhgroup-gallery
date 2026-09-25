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
        <div class="card-body px-3">
            <div class="table-responsive-sm">
                <table class="table table-borderless table-striped mb-0">
                    <tbody>
                        <tr>
                            <th width="200">Investor Name</th>
                            <th width="10">:</th>
                            <td>{{ @$data->name }}</td>
                        </tr>
                        <tr>
                            <th width="200">Phone No.</th>
                            <th width="10">:</th>
                            <td>{{ @$data->phone }}</td>
                        </tr>
                        <tr>
                            <th width="200">Email Address</th>
                            <th width="10">:</th>
                            <td>{{ @$data->email }}</td>
                        </tr>
                        <tr>
                            <th width="200">NID No.</th>
                            <th width="10">:</th>
                            <td>{{ @$data->nid }}</td>
                        </tr>
                        <tr>
                            <th width="200">Address</th>
                            <th width="10">:</th>
                            <td>{{ @$data->address }}</td>
                        </tr>
                        <tr>
                            <th width="200">Profit Percent</th>
                            <th width="10">:</th>
                            <td>{{ @$data->profit_percentage }}</td>
                        </tr>
                        <tr>
                            <th width="200">bKash Account</th>
                            <th width="10">:</th>
                            <td>{{ @$data->bkash }}</td>
                        </tr>
                        <tr>
                            <th width="200">Nagad Account</th>
                            <th width="10">:</th>
                            <td>{{ @$data->nagad }}</td>
                        </tr>
                        <tr>
                            <th width="200">Bank - Branch</th>
                            <th width="10">:</th>
                            <td>{{ @$data->bank }}</td>
                        </tr>
                        <tr>
                            <th width="200">Account Name</th>
                            <th width="10">:</th>
                            <td>{{ @$data->account_name }}</td>
                        </tr>
                        <tr>
                            <th width="200">Account Number</th>
                            <th width="10">:</th>
                            <td>{{ @$data->account_no }}</td>
                        </tr>
                        @if (file_exists($data->image))
                            <tr>
                                <th width="200">Image</th>
                                <th width="10">:</th>
                                <td>
                                    <a href="{{ asset($data->image) }}" data-fancybox="">
                                        <img src="{{ asset($data->image) }}" height="50" alt="Image">
                                    </a>
                                </td>
                            </tr>
                        @endif
                        @if (file_exists($data->document))
                            <tr>
                                <th width="200">Document</th>
                                <th width="10">:</th>
                                <td>
                                    <a href="{{ asset($data->document) }}" data-fancybox="">
                                        <img src="{{ asset($data->document) }}" height="50" alt="Document">
                                    </a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
