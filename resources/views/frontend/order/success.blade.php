@extends('layouts.frontend.app')

@section('content')
<div class="container py-5">

    <div class="text-center mb-4">
        <span style="font-size:70px;">✅</span>
        <h2 class="fw-bold text-success mt-3">
            Order Placed Successfully!
        </h2>
        <p class="text-muted">
            Thank you for your order. We will contact you soon.
        </p>
    </div>

    <div class="card shadow-sm mx-auto" style="max-width:800px">
        <div class="card-body">

            <h5 class="mb-3">
                Order Number:
                <span class="text-danger">{{ $order->order_number }}</span>
            </h5>

            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td class="text-end">
                                ৳ {{ number_format($item->price,2) }}
                            </td>
                            <td class="text-end">
                                ৳ {{ number_format($item->total,2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-end">
                <h5>
                    Grand Total:
                    <span class="text-danger">
                        ৳ {{ number_format($order->total,2) }}
                    </span>
                </h5>
            </div>

            <hr>

            <div class="d-flex justify-content-between">
                <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                    Continue Shopping
                </a>

                <a href="{{ route('orders.index') }}" class="btn btn-danger">
                    My Orders
                </a>
            </div>

        </div>
    </div>

</div>
@endsection
