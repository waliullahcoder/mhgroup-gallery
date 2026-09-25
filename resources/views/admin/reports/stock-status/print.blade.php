@extends('layouts.admin.print_app')

@section('content')
    <table class="table table-bordered table-condensed table-striped align-middle mb-3">
        @php $key = 1; @endphp
        <thead class="text-nowrap">
            <tr>
                <th class="text-center" width="30">SL#</th>
                <th>Product</th>
                <th class="text-center" width="50">Opening</th>
                <th class="text-center" width="50">Production</th>
                <th class="text-center" width="50">Sales</th>
                <th class="text-center" width="50">Sales Return</th>
                <th class="text-center" width="50">Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td class="text-center">{{ $key++ }}</td>
                    <td>{{ $row['product'] }} @if (!empty($row['edition']))
                            <span class="text-muted">({{ $row['edition'] }})</span>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($row['opening'], 2) }}</td>
                    <td class="text-center">{{ number_format($row['production'], 2) }}</td>
                    <td class="text-center">{{ number_format($row['sales'], 2) }}</td>
                    <td class="text-center">{{ number_format($row['sales_return'], 2) }}</td>
                    <td class="text-center">{{ number_format($row['stock'], 2) }}</td>
                </tr>
            @endforeach
            @if (count($data) == 0)
                <tr>
                    <td colspan="7" class="text-center">No data found</td>
                </tr>
            @endif
        </tbody>
    </table>
    <div style="padding-top: 30px;">Print Date & Time : {{ date('d-m-Y h:i:s A') }}</div>
@endsection
