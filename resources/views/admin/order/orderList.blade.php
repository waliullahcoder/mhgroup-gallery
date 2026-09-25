@extends('layouts.admin.index_app')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- MAIN CONTENT -->
        <div class="col-lg-12">
            {{-- ORDER LIST --}}
            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">🧾 Orders</h5>

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
                                    <th>Order No</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $order->order_number }}</strong>
                                        </td>
                                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                                        <td>৳ {{ number_format($order->total,2) }}</td>
                                        <td class="text-capitalize">
                                            {{ $order->payment_method }}
                                        </td>
                                       <td>
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
                                            </span>
                                        </td>

                                        <td>
                                            {{ $order->created_at->format('d M Y') }}
                                        </td>
                                         <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.orders.show',$order->id) }}" class="btn btn-outline-primary">👁</a>
                                            <a href="{{ route('admin.orders.invoice',$order->id) }}" class="btn btn-outline-success" target="_blank">📄</a>
                                            <a href="{{ route('admin.orders.track',$order->id) }}" class="btn btn-outline-warning">🔄</a>
                                            <!-- STATUS MODAL BUTTON -->
                                            @if(!in_array($order->status, ['delivered', 'cancelled']))
                                            <button
                                                class="btn btn-outline-dark"
                                                data-bs-toggle="modal"
                                                data-bs-target="#statusModal{{ $order->id }}">
                                                ⚙️
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            No orders found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Paginator section --}}
                        <style>
                        .pagination .page-link {
                            color: #333;
                            border-radius: 6px;
                        }

                        .pagination .page-item.active .page-link {
                            background: #0d6efd;
                            border-color: #0d6efd;
                            color: #fff;
                        }

                        .pagination .page-link:hover {
                            background: #f1f1f1;
                        }
                        </style>
                         @if ($orders->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                <nav>
                                    <ul class="pagination pagination-sm">

                                        {{-- Previous --}}
                                        @if ($orders->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">«</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">«</a>
                                            </li>
                                        @endif

                                        {{-- Page Numbers --}}
                                        @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                                            <li class="page-item {{ $page == $orders->currentPage() ? 'active' : '' }}">
                                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                            </li>
                                        @endforeach

                                        {{-- Next --}}
                                        @if ($orders->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">»</a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link">»</span>
                                            </li>
                                        @endif

                                    </ul>
                                </nav>
                            </div>
                        @endif
                        {{-- End paginator --}}

                        @forelse($orders as $order)
                        <!-- STATUS UPDATE MODAL -->
                        <div class="modal fade" id="statusModal{{ $order->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <form method="POST"
                                    action="{{ route('admin.orders.updateStatus', $order->id) }}"
                                    class="modal-content">
                                    @csrf

                                    <div class="modal-header">
                                        <h5 class="modal-title">🔄 Update Order Status</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <p class="mb-2">
                                            <strong>Order:</strong> {{ $order->order_number }}
                                        </p>

                                        <div class="form-group">
                                        <label class="form-label">Select Status</label>

                                        <select name="status"
                                                class="form-select status-select status-{{ $order->status }}"
                                                required>

                                            <option value="">-- Choose Status --</option>
                                            @if($order->status == 'pending')
                                            <option value="pending"
                                                {{ $order->status=='pending'?'selected':'' }}>
                                                Pending
                                            </option>
                                            <option value="processing"
                                                {{ $order->status=='processing'?'selected':'' }}>
                                                Processing
                                            </option>
                                            <option value="cancelled"
                                                {{ $order->status=='cancelled'?'selected':'' }}>
                                                Cancel
                                            </option>
                                            @endif
                                            @if($order->status == 'processing')
                                            <option value="processing"
                                                {{ $order->status=='processing'?'selected':'' }}>
                                                Processing
                                            </option>
                                            <option value="confirmed"
                                                {{ $order->status=='confirmed'?'selected':'' }}>
                                                Confirmed
                                            </option>
                                            @endif
                                            @if($order->status == 'confirmed')
                                            <option value="confirmed"
                                                {{ $order->status=='confirmed'?'selected':'' }}>
                                                Confirmed
                                            </option>
                                            <option value="shipped"
                                                {{ $order->status=='shipped'?'selected':'' }}>
                                                Shipped
                                            </option>
                                            @endif
                                            @if($order->status == 'shipped')
                                            <option value="shipped"
                                                {{ $order->status=='shipped'?'selected':'' }}>
                                                Shipped
                                            </option>
                                            <option value="delivered"
                                                {{ $order->status=='delivered'?'selected':'' }}>
                                                Delivered
                                            </option>
                                            @endif
                                            @if($order->status == 'delivered')
                                            <option value="delivered"
                                                {{ $order->status=='delivered'?'selected':'' }}>
                                                Delivered
                                            </option>
                                            @endif

                                            

                                        </select>
                                    </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Update Status</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                         @empty
                         No orders found
                            <!-- STATUS UPDATE MODAL \\-->
                        @endforelse
                    </div>


                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.status-select').forEach(select => {

        function updateBg() {
            select.classList.remove(
                'status-pending',
                'status-processing',
                'status-shipped',
                'status-delivered',
                'status-cancelled'
            );

            if (select.value) {
                select.classList.add('status-' + select.value);
            }
        }

        // initial load
        updateBg();

        // on change
        select.addEventListener('change', updateBg);
    });
});
</script>

@endsection
