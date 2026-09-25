@extends('layouts.admin.app')

@section('content')
<div class="container">
    <div class="card border-dashed mb-3">
        <div class="card-header">
            <h5>Purchase Order Details (PO No: {{ $purchaseOrder->po_number }})</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4"><strong>Supplier:</strong> {{ $purchaseOrder->vendor->name ?? '-' }}</div>
                <div class="col-md-4"><strong>Store:</strong> {{ $purchaseOrder->store->name ?? '-' }}</div>
                <div class="col-md-4"><strong>Status:</strong> {{ ucfirst($purchaseOrder->status) }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4"><strong>Order Date:</strong> {{ $purchaseOrder->order_date }}</div>
                <div class="col-md-4"><strong>Expected Date:</strong> {{ $purchaseOrder->expected_date }}</div>
                <div class="col-md-4"><strong>Payment Type:</strong> {{ $purchaseOrder->payment_type }}</div>
            </div>

            {{-- Items Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Cost</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $counter = 1; @endphp
                        @foreach($purchaseOrder->items as $item)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                            <td>৳{{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>৳{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="mt-3">
                <ul class="list-group rounded-0">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Sub Total:</span>
                        <span>৳{{ number_format($purchaseOrder->total_amount, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Tax:</span>
                        <span>৳{{ number_format($purchaseOrder->tax_amount, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Discount:</span>
                        <span>৳{{ number_format($purchaseOrder->discount_amount, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Grand Total:</strong>
                        <strong>৳{{ number_format($purchaseOrder->grand_total, 2) }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Paid Amount:</span>
                        <span>৳{{ number_format($purchaseOrder->paid_amount, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Due Amount:</span>
                        <span>৳{{ number_format($purchaseOrder->due_amount, 2) }}</span>
                    </li>
                </ul>
            </div>

            {{-- Notes --}}
            @if($purchaseOrder->notes)
            <div class="mt-3">
                <strong>Comments:</strong>
                <p>{{ $purchaseOrder->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <a href="{{ route('admin.purchase-order.index') }}" class="btn btn-secondary">Back to List</a>
</div>
@endsection