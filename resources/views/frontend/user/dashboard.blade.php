@extends('layouts.frontend.app')

@section('content')
<div class="container py-5">
    <div class="row">

        <!-- SIDEBAR -->
       @include('frontend.user.userSideBar')

        <!-- MAIN CONTENT -->
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="mb-3">👋 Welcome, {{ auth()->user()->name }}</h4>
                    <p class="text-muted">
                        This is your user dashboard. From here you can see orders, Invoice, Payment
                        and manage your profile.
                    </p>

                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <div class="card text-center">
                                 <a href="{{ route('orders.index') }}">
                                <div class="card-body">
                                    <h5>Orders</h5>
                                    <b>{{ auth()->user()->orders()->count() }}</b>
                                </div>
                                 </a>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card text-center">
                                <a href="{{ route('wishlist.index') }}">
                                <div class="card-body">
                                    <h5>Wishlist</h5>
                                    <b> {{ auth()->user()->wishlists()->count() }}</b>
                                </div>
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card text-center">
                                <a href="{{route('frontend.user.invoiceHistory')}}">
                                <div class="card-body">
                                    <h5>Invoice Amount</h5>
                                    <b>৳{{number_format($sales,2)}}</b>
                                </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Purchase Amount</h5>
                                    <b>৳{{number_format($sales,2)}}</b>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Paid</h5>
                                    <b>৳{{number_format($collection,2)}}</b>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Due</h5>
                                    <b>৳{{number_format($sales-$collection,2)}}</b>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
