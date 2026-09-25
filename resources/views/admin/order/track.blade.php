@extends('layouts.admin.index_app')
@section('content')
<div class="container py-5">
    <div class="row">

    
        {{-- MAIN --}}
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    <h4 class="mb-2">📦 Order Tracking</h4>
                    <p class="text-muted mb-4">
                        Order No: <strong>{{ $order->order_number }}</strong>
                    </p>

                    {{-- STATUS BAR --}}
                   @php
                        $steps = ['pending', 'processing', 'confirmed', 'shipped', 'delivered'];

                        $statusColors = [
                            'pending'    => 'warning',
                            'processing' => 'primary',
                            'confirmed'  => 'info',
                            'shipped'    => 'secondary',
                            'delivered'  => 'success',
                            'cancelled'  => 'danger',
                        ];

                        $currentIndex = array_search($order->status, $steps);
                    @endphp

                    @if($order->status != 'cancelled')
                    <div class="order-track mb-5">
                        @foreach($steps as $index => $step)
                            @php
                                $isActive = $currentIndex !== false && $index <= $currentIndex;
                                $colorClass = $isActive
                                    ? 'track-' . ($statusColors[$step] ?? 'secondary')
                                    : '';
                            @endphp

                            <div class="track-step {{ $isActive ? 'active '.$colorClass : '' }}">
                                <div class="circle">
                                    @if($isActive)
                                        ✔
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                <div>
                                    {{ ucfirst($step) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                     @else
                    <div class="mb-5">
                        <span class="btn btn-danger">Your order is CANCELLED. We are sorry for the inconvenience.</span>
                    </div>
                    @endif


                    {{-- ORDER INFO --}}
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Order Info</h6>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Status</span>
                                  <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                    {{ ucfirst($order->status) }}
                                </span>

                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Total</span>
                                    <strong>৳ {{ number_format($order->total,2) }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Placed At</span>
                                    <span>{{ $order->created_at->format('d M Y, h:i A') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                            ← Back to Orders
                        </a>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

@endsection
