@extends('layouts.frontend.app')

@section('content')
<div class="container py-5">
    <div class="row">

        {{-- SIDEBAR --}}
        @include('frontend.user.userSideBar')

        <div class="col-lg-9">

            <h4 class="mb-4">❤️ My Wishlist</h4>

            @if($wishlists->count())
                <div class="row g-3">

                  @foreach($wishlists as $item)
                    @php
                        $product = $item->product;
                    @endphp

                    <div class="p-sm-2 p-1" style="width:25%;">
                        <div class="product-card-wrapper">
                            <div class="product-card">

                                {{-- DISCOUNT --}}
                                @if($product->discount)
                                    <div class="discount-badge">
                                        <span class="product-discount">
                                            {{ number_format($product->discount) }}%
                                        </span>
                                    </div>
                                @endif

                                {{-- PRODUCT LINK --}}
                                <a class="z-2" href="{{ route('product.details', $product->id) }}">
                                    <figure class="product-card-image ratio"
                                            style="--bs-aspect-ratio: 130%">
                                        <img class="object-fit-contain product-img"
                                            src="{{ asset($product->thumbnail) }}"
                                            alt="{{ $product->name }}">
                                    </figure>

                                    <div class="product-card-content">
                                        <h6 class="h6 product-card-title truncate-text"
                                            style="--lines: 2;">
                                            {{ $product->name }}
                                        </h6>
                                        <b class="product-card-title truncate-text"
                                            style="--lines: 2;">
                                            CODE-{{ $product->id }}
                                        </b>

                                        <p class="product-card-author truncate-text"
                                        style="--lines: 2;">
                                            {!! $product->short_description !!}
                                        </p>

                                        <span class="product-card-price">

                                            {{-- SALE PRICE --}}
                                            @if($product->sale_price)
                                                <del>
                                                    <span class="Price-amount">
                                                        {{ number_format($product->sale_price, 2) }}
                                                        <span class="Price-currencySymbol">৳</span>
                                                    </span>
                                                </del>
                                            @endif

                                            {{-- REGULAR PRICE --}}
                                            <ins>
                                                <span class="Price-amount">
                                                    {{ number_format($product->regular_price, 2) }}
                                                    <span class="Price-currencySymbol">৳</span>
                                                </span>
                                            </ins>

                                        </span>
                                    </div>
                                </a>

                                {{-- ACTIONS --}}
                                <div class="product-card-action d-flex gap-1">

                                    {{-- Add + --}}
                                    <button class="btn btn-sm btn-danger add-to-cart"
                                            data-id="{{ $product->id }}">
                                        Add +
                                    </button>

                                    {{-- REMOVE FROM WISHLIST --}}
                                    <form action="{{ route('wishlist.destroy', $item->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-secondary">
                                            ❌
                                        </button>
                                    </form>

                                </div>

                            </div>
                        </div>
                    </div>

                    @endforeach

                </div>
            @else
                <p class="text-muted text-center">
                    Your wishlist is empty 😢
                </p>
            @endif

        </div>
    </div>
</div>
@endsection
