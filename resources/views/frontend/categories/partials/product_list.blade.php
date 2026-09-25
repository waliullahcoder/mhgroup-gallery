@foreach($subcategories as $sub)
<style>
    .filtered-products {
        width:25%;
    }
</style>
    <div class="section-card" style="margin-top:2px;">
                    <!-- Section Header -->
                    <div class="section-header mb-3">
                        <h5 class="section-title">{{$sub->name}}</h5>
                        <a href="{{route('category.singleCategoryPage', $sub->id)}}" class="section-link">See All</a>
                    </div>

                    <div class="position-relative">
                        <!-- Swiper -->
                        <div class="swiper carousel"
                             data-items="2"
                             data-xl-items="6"
                             data-lg-items="4"
                             data-md-items="4"
                             data-sm-items="3"
                             data-xs-items="2"
                             data-margin="4"
                             data-dots="false"
                             data-arrows="true">

                            <div class="swiper-wrapper">

                                <!-- Product Slide -->
                              @if($sub->products->count()> 0)
                               @foreach($sub->products as $product)
                                <div class="swiper-slide filtered-products">
                                    <div class="p-sm-2 p-1">
                                        <div class="product-card-wrapper">
                                            <div class="product-card">
                                                @if($product->discount>0)
                                                <div class="{{ $product->discount_type == 'percent' ? 'discount-badge' : 'discount-badge-flat' }}">
                                                    <span class="product-discount">{{number_format($product->discount)}}</span>
                                                </div>
                                                @endif
                                        <a class="z-2" href="{{route('product.details', $product->id)}}">
                                                <figure class="product-card-image ratio" style="--bs-aspect-ratio: 100%">
                                                  
                                                    <img class="object-fit-contain product-img"
                                                        src="{{ asset($product->thumbnail) }}"
                                                         alt="">
                                                </figure>

                                                <div class="product-card-content">
                                                    <h6 class="h6 product-card-title truncate-text" style="--lines: 1;">
                                                      
                                                         {{$product->name}}
                                                    </h6>
                                                    <p class="product-card-title truncate-text"
                                                        style="--lines: 1;">
                                                        {{ $product->code }}
                                                </p>
                                                    <span class="product-card-price">
                                                        @if($product->discount>0)
                                                        <del>
                                                            <span class="Price-amount">
                                                                 {{ number_format($product->regular_price, 2) }} <span class="Price-currencySymbol">৳</span>
                                                            </span>
                                                        </del>
                                                        @endif
                                                        <ins>
                                                            <span class="Price-amount">
                                                                 {{ number_format($product->sale_price, 2) }} <span class="Price-currencySymbol">৳</span>
                                                            </span>
                                                        </ins>
                                                    </span>
                                                </div>
                                                </a>
                                                <div class="product-card-action">
                                                   <button class="btn btn-sm btn-danger add-to-cart"
                                                        data-variant_id="{{ $product->variants[0]->id ?? null }}"
                                                        data-id="{{ $product->id }}">
                                                    Add +
                                                </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                               @endforeach
                                @else
                                    @include('frontend.categories.partials.notfound')
                                @endif
                                <!-- Product Slide -->
                           

                            </div>

                            <!-- Swiper Arrows -->
                           <div class="swiper-arrow-btn swiper-button-prev" data-target="#category-swiper">
                        <svg width="11" height="16" viewBox="0 0 11 16" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.7637 0.333588C10.1815 0.741772 10.1815 1.42208 9.77223 1.83026L3.5905 7.99554L9.7637 14.1608C10.1815 14.5775 10.1815 15.2493 9.7637 15.666C9.3459 16.0827 8.67231 16.0827 8.25451 15.666L1.16897 8.59931C0.836434 8.26767 0.836434 7.73192 1.16897 7.40027L8.25451 0.333588C8.67231 -0.0831001 9.3459 -0.0831001 9.7637 0.333588Z"
                                fill="#333333"></path>
                        </svg>
                    </div>
                    <div class="swiper-arrow-btn swiper-button-next" data-target="#category-swiper">
                        <svg width="11" height="16" viewBox="0 0 11 16" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.7637 0.333588C10.1815 0.741772 10.1815 1.42208 9.77223 1.83026L3.5905 7.99554L9.7637 14.1608C10.1815 14.5775 10.1815 15.2493 9.7637 15.666C9.3459 16.0827 8.67231 16.0827 8.25451 15.666L1.16897 8.59931C0.836434 8.26767 0.836434 7.73192 1.16897 7.40027L8.25451 0.333588C8.67231 -0.0831001 9.3459 -0.0831001 9.7637 0.333588Z"
                                fill="#333333"></path>
                        </svg>
                    </div>

                        </div>
                        <!-- End Swiper -->

                    </div>
                </div>
@endforeach