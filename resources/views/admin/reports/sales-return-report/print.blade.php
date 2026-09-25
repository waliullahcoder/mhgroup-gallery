@extends('layouts.admin.print_app')

@section('content')
    @if ($report_type == 'product_history')
        <table class="table table-bordered table-condensed table-striped align-middle mb-3">
            <thead class="text-nowrap">
                <tr>
                    <th class="text-center" width="20">SL#</th>
                    <th>Return No</th>
                    <th>Date</th>
                    <th>Store</th>
                    <th>Vendor</th>
                    <th>Product</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ @$row->purchase_return->return_no }}</td>
                        <td class="text-nowrap">{{ @$row->purchase_return->formattedDate }}</td>
                        <td>{{ @$row->store->name }}</td>
                        <td>{{ @$row->vendor->name }}</td>
                        <td>{{ @$row->product->name }}</td>
                        <td class="text-right">{{ number_format($row->qty, 2) }}</td>
                        <td class="text-right">{{ number_format($row->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" class="text-right">Total</th>
                    <th class="text-right">{{ number_format($data->sum('amount'), 2) }}
                    </th>
                </tr>
            </tfoot>
        </table>
    @elseif ($report_type == 'product_summary')
        <table class="table table-bordered table-condensed table-striped align-middle mb-3">
            <thead class="text-nowrap">
                <tr>
                    <th class="text-center" width="20">SL#</th>
                    <th>Product</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ @$row->product->name }}</td>
                        <td class="text-right">{{ number_format($row->sum_quantity, 2) }}</td>
                        <td class="text-right">{{ number_format($row->sum_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="text-right">Total</th>
                    <th class="text-right">{{ number_format($data->sum('sum_quantity'), 2) }}</th>
                    <th class="text-right">{{ number_format($data->sum('sum_amount'), 2) }}</th>
                </tr>
            </tfoot>
        </table>
    @else
        <table class="table table-bordered table-condensed table-striped align-middle mb-3">
            <thead class="text-nowrap">
                <tr>
                    <th class="text-center" width="20">SL#</th>
                    <th>Return No</th>
                    <th>Date</th>
                    <th>Vendor</th>
                    <th>Store</th>
                    <th>Products</th>
                    <th class="text-right">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ @$row->return_no }}</td>
                        <td class="text-nowrap">{{ @$row->formattedDate }}</td>
                        <td>{{ @$row->vendor->name }}</td>
                        <td>{{ @$row->store->name }}</td>
                        <td>
                            @foreach ($row->list as $item)
                                {{ @$item->product->name }}
                                <span class="text-dark"> x {{ number_format($item->qty) }}
                                    {{ @$item->product->uom->name }}</span>
                                <br>
                            @endforeach
                        </td>
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
