@foreach($data as $subCategory)
<div class="category-product-section pb-4">
    <div class="container">
        <div class="section-card">
            <div class="section-header mb-3">
                <h5 class="section-title">{{ $subCategory->name }}</h5>
                <a href="{{ route('category.singleCategoryPage', $subCategory->id) }}" class="section-link">
                    See All
                </a>
            </div>

            <div class="position-relative">
                <div class="swiper carousel" data-items="2" data-xl-items="6" data-lg-items="5"
                    data-md-items="4" data-sm-items="3" data-xs-items="2"
                    data-margin="5" data-dots="false" data-arrows="true">

                    <div class="swiper-wrapper">
                        @foreach($subCategory->products as $product)
                            <div class="swiper-slide">
                                <div class="p-sm-2 p-1">
                                    <div class="product-card-wrapper">
                                        <div class="product-card">
                                            <a href="{{ route('product.details', $product->id) }}">
                                                <figure class="product-card-image ratio" style="--bs-aspect-ratio: 100%">
                                                    <img class="object-fit-contain product-img"
                                                        loading="lazy"
                                                        src="{{ asset($product->thumbnail) }}"
                                                        alt="{{ $product->name }}">
                                                </figure>

                                                <div class="product-card-content">
                                                    <h6 class="h6 product-card-title truncate-text" style="--lines: 1;">
                                                        {{ $product->name }}
                                                    </h6>

                                                    <p class="product-card-title truncate-text" style="--lines: 1;">
                                                        {{ $product->code }}
                                                    </p>

                                                    <span class="product-card-price">
                                                        @if($product->discount > 0)
                                                            <del>{{ number_format($product->regular_price) }} ৳</del>
                                                        @endif

                                                        <ins>{{ number_format($product->sale_price) }} ৳</ins>
                                                    </span>
                                                </div>
                                            </a>

                                            <div class="product-card-action">
                                                <button class="btn btn-sm btn-danger add-to-cart"
                                                    data-id="{{ $product->id }}">
                                                    Add +
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @include('layouts.frontend.partial.svgbutton')
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach