@extends('layouts.admin.index_app')


@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">
                        <h5>Order #{{ $order->order_number }}</h5>
                        <a href="{{ route('admin.orders.invoice', $order) }}"
                           class="btn btn-sm btn-outline-primary">
                            📄 Download Invoice
                        </a>
                    </div>

                    {{-- STATUS TRACK --}}
                    <ul class="order-tracker mb-4">
                        <li>Sir/Madam, {{ $order->user->name ?? 'N/A' }}</li>
                       <span class="badge 
                                                @switch($order->status)
                                                    @case('pending') bg-warning @break
                                                    @case('processing') bg-primary @break
                                                    @case('confirmed') bg-success @break
                                                    @case('shipped') bg-info @break
                                                    @case('delivered') bg-secondary text-dark @break
                                                    @case('cancelled') bg-danger @break
                                                    @default bg-secondary
                                                @endswitch
                                            ">
                                                {{ ucfirst($order->status) }}
                                            </span><br><br>
                                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary">Orders</a>
                    </ul>
                    {{-- CALCULATION --}}
                    @php
                        $subtotal = $order->items->sum('total');
                        $discount = $order->discount;
                        $afterDiscount = $subtotal - $discount;
                        $tax = $order->tax;
                        $grandTotal = $afterDiscount + $tax;
                    @endphp
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>
                                        {{ $item->product->category->name ?? '-' }}
                                    </td>
                                    <td>{{ $item->qty }}</td>
                                    <td>৳ {{ number_format($item->price,2) }}</td>
                                    <td>৳ {{ number_format($item->total,2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{-- TOTAL --}}
                

                    <div class="text-end mt-3">
                        <table>
                                        <tr>
                                            <td>Total</td>
                                            <td style="text-align:right;">- ৳{{ number_format($subtotal,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Discount {{$settings->discount_type=='percent' ? '('.$settings->discount.'%'.')' : ''}}</td>
                                            <td style="text-align:right;">- ৳{{ number_format($discount,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tax ({{$settings->tax}}%)</td>
                                            <td style="text-align:right;">- ৳{{ number_format($tax,2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Grand Total  </th>
                                            <th style="text-align:right;">- ৳{{ number_format($grandTotal,2) }}</th>
                                        </tr>
                                    </table>
                        <h5>Net Total: ৳ {{ number_format($order->total,2) }}</h5>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
