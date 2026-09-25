@extends('layouts.frontend.app')

@section('content')
<div class="container py-5">
    <div class="row">

        <!-- SIDEBAR -->
        @include('frontend.user.userSideBar')

        <!-- MAIN CONTENT -->
        <div class="col-lg-9">
            {{-- ORDER LIST --}}
            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">🧾 Invoice History</h5>

                        {{-- SEARCH --}}
                        <form method="GET" class="d-flex">
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   class="form-control form-control-sm me-2"
                                   placeholder="Search order...">
                            <button class="btn btn-sm btn-outline-secondary">
                                Search
                            </button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Invoice No.</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Discount</th>
                                    <th>Tax</th>
                                    <th>Due</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $order)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $order->invoice }}</strong></td>
                                        <td>{{ $order->date }}</td>
                                        <td>৳{{ number_format($order->net_amount,2) }}</td>
                                        <td>৳{{ number_format($order->paid,2) }}</td>
                                        <td>৳{{ number_format($order->discount,2) }}</td>
                                        <td>৳{{ number_format($order->tax_amount,2) }}</td>
                                        <td>৳{{ number_format(($order->net_amount-$order->paid),2) }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                            <a href="{{ route('frontend.user.salesInvoice',$order->id) }}" class="btn btn-outline-success" target="_blank">📄</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
                                            No orders found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>

        </div>
    </div>
</div>
@endsection
