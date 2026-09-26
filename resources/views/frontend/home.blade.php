@extends('layouts.frontend.app')

@section('content')
<style>
 

.featured-category-img img {
    transition: all 0.4s ease;
    animation: categoryFloat 3s ease-in-out infinite;
}

/* হালকা উপরে-নিচে ভাসবে */
@keyframes categoryFloat {
    0%, 100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-6px);
    }
}

/* Mouse hover করলে একটু বেশি সুন্দর effect */
 .featured-category-img img {
    transform: translateY(-8px) scale(1.08);
    filter: drop-shadow(0 8px 10px rgba(0, 0, 0, 0.15));
}

    </style>
    {{-- Hero Section --}}
    <div class="hero-section  animate__animated animate__fadeInDown">
        <div class="container">
            <div class="swiper carousel" data-items="1" data-sm-items="1" data-xs-items="1"
                data-autoplay="true" data-dots="true" data-arrows="true" data-loop="true">

                <div class="swiper-wrapper">
                    @foreach ($slides as $item)
                        <div class="swiper-slide">
                            @if ($item->link)
                                <a href="{{ $item->link }}">
                                    <img class="w-100"
                                        src="{{ asset($item->image) }}"
                                        srcset="{{ asset($item->mobile_image) }} 768w, {{ asset($item->image) }} 1200w"
                                        sizes="(max-width: 768px) 100vw, 1200px"
                                        alt="Slide Image">
                                </a>
                            @else
                                <img class="w-100"
                                    src="{{ asset($item->image) }}"
                                    srcset="{{ asset($item->mobile_image) }} 768w, {{ asset($item->image) }} 1200w"
                                    sizes="(max-width: 768px) 100vw, 1200px"
                                    alt="Slide Image">
                            @endif
                        </div>
                    @endforeach
                </div>

                @if (count($slides) > 1)
                    <div class="swiper-arrow-btn swiper-button-prev">
                        <svg width="11" height="16" viewBox="0 0 11 16" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.7637 0.333588C10.1815 0.741772 10.1815 1.42208 9.77223 1.83026L3.5905 7.99554L9.7637 14.1608C10.1815 14.5775 10.1815 15.2493 9.7637 15.666C9.3459 16.0827 8.67231 16.0827 8.25451 15.666L1.16897 8.59931C0.836434 8.26767 0.836434 7.73192 1.16897 7.40027L8.25451 0.333588C8.67231 -0.0831001 9.3459 -0.0831001 9.7637 0.333588Z"
                                fill="#333333"></path>
                        </svg>
                    </div>

                    <div class="swiper-arrow-btn swiper-button-next">
                        <svg width="11" height="16" viewBox="0 0 11 16" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.7637 0.333588C10.1815 0.741772 10.1815 1.42208 9.77223 1.83026L3.5905 7.99554L9.7637 14.1608C10.1815 14.5775 10.1815 15.2493 9.7637 15.666C9.3459 16.0827 8.67231 16.0827 8.25451 15.666L1.16897 8.59931C0.836434 8.26767 0.836434 7.73192 1.16897 7.40027L8.25451 0.333588C8.67231 -0.0831001 9.3459 -0.0831001 9.7637 0.333588Z"
                                fill="#333333"></path>
                        </svg>
                    </div>
                @endif

                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
    {{-- End Hero --}}

    
 
 {{-- All header --}}
@foreach($get_sub_category_all_header_product_only as $subCategory)
 <div class="category-product-section pb-4 animate__animated animate__fadeInUp">
        <div class="container">
            <div class="section-card">
                <div class="section-header mb-3">
                    <h5 class="section-title"> {{ $subCategory->name }}</h5>
                    <a href="{{route('category.singleCategoryPage', $subCategory->id)}}" class="section-link">See All</a>
                </div>
                <div class="position-relative">
                    <div class="swiper carousel" data-items="2" data-xl-items="6" data-lg-items="5" data-md-items="4"
                        data-sm-items="3" data-xs-items="2" data-margin="5" data-dots="false" data-arrows="true">
                        <div class="swiper-wrapper">
                             @if($subCategory->products->count() > 0)
                               @foreach($subCategory->products as $product)
                            <div class="swiper-slide">
                                <div class="p-sm-2 p-1">
                                    <div class="product-card-wrapper">
                                        <div class="product-card">
                                             @if($product->discount>0)
                                            <div class="discount-badge">
                                                <span class="product-discount">{{number_format($product->discount)}}</span>
                                            </div>
                                            @endif
                                           <a href="{{route('product.details', $product->id)}}">
                                            <figure class="product-card-image ratio" style="--bs-aspect-ratio: 100%">
                                                <img class="object-fit-contain product-img"  
                                                src="{{ asset($product->thumbnail) }}"
                                                    alt="{{ $product->name }}" 
                                                    fetchpriority="high"
                                                    decoding="async">
                                            </figure>
                                            <div class="product-card-content">
                                                <h6 class="h6 product-card-title truncate-text" style="--lines: 2;">
                                                        {{ $product->name }}
                                                    
                                                </h6>
                                                <p class="product-card-title truncate-text"
                                                        style="--lines: 2;">
                                                        {{ $product->code }}
                                                </p>
                                                <span class="product-card-price">
                                                    @if($product->discount>0)
                                                    <del>
                                                         <span class="Price-amount">
                                                                 {{ number_format($product->regular_price) }} <span class="Price-currencySymbol">৳</span>
                                                            </span>
                                                    </del>
                                                    @endif
                                                    <ins>
                                                        <span class="Price-amount"> {{ number_format($product->sale_price) }}<span class="Price-currencySymbol">৳</span>
                                                        </span>
                                                    </ins>
                                                </span>
                                            </div>
                                            </a>
                                            <div class="product-card-action">
                                                <button class="btn btn-sm btn-danger add-to-cart"
                                                data-variant-id="{{ $product->variants->first()?->id }}"
                                                            data-id="{{ $product->id }}">
                                                           <span>+</span>
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
                        </div>
                        
                       @include('layouts.frontend.partial.svgbutton')
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    

    {{-- Lazy Loaded Sections --}}
    <div id="lazy-niog">
        <div class="text-center py-3 lazy-loader">Loading...</div>
    </div>

    <div id="lazy-trending">
        <div class="text-center py-3 lazy-loader">Loading...</div>
    </div>

    <div id="lazy-sian">
        <div class="text-center py-3 lazy-loader">Loading...</div>
    </div>

    <!-- <div id="lazy-writers">
        <div class="text-center py-3 lazy-loader">Loading...</div>
    </div> -->
@endsection


@push('js')
<script>
   
document.addEventListener("DOMContentLoaded", function () {
    

    const sections = [
        { id: 'lazy-niog', type: 'niog' },
        { id: 'lazy-trending', type: 'trending' },
        { id: 'lazy-sian', type: 'sian' },
        { id: 'lazy-writers', type: 'writers' },
    ];

    function reInitSwiper() {
        if (typeof initSwiper === 'function') {
            initSwiper();
            return;
        }

        if (typeof window.initSwiper === 'function') {
            window.initSwiper();
            return;
        }

        if (typeof Swiper !== 'undefined') {
            document.querySelectorAll('.swiper.carousel').forEach(function (el) {
                if (el.classList.contains('swiper-initialized')) {
                    return;
                }

                new Swiper(el, {
                    slidesPerView: Number(el.dataset.items || 2),
                    spaceBetween: Number(el.dataset.margin || 5),
                    loop: el.dataset.loop === 'true',
                    autoplay: el.dataset.autoplay === 'true'
                        ? { delay: 3000, disableOnInteraction: false }
                        : false,
                    pagination: el.dataset.dots === 'true'
                        ? {
                            el: el.querySelector('.swiper-pagination'),
                            clickable: true
                        }
                        : false,
                    navigation: el.dataset.arrows === 'true'
                        ? {
                            nextEl: el.querySelector('.swiper-button-next'),
                            prevEl: el.querySelector('.swiper-button-prev')
                        }
                        : false,
                    breakpoints: {
                        320: {
                            slidesPerView: Number(el.dataset.xsItems || el.dataset.items || 2)
                        },
                        576: {
                            slidesPerView: Number(el.dataset.smItems || el.dataset.items || 2)
                        },
                        768: {
                            slidesPerView: Number(el.dataset.mdItems || el.dataset.items || 3)
                        },
                        992: {
                            slidesPerView: Number(el.dataset.lgItems || el.dataset.items || 4)
                        },
                        1200: {
                            slidesPerView: Number(el.dataset.xlItems || el.dataset.items || 5)
                        }
                    }
                });
            });
        }
    }

    function loadSection(index) {
        if (index >= sections.length) {
            return;
        }

        let section = sections[index];
        let target = document.getElementById(section.id);

        if (!target) {
            loadSection(index + 1);
            return;
        }

        fetch("{{ url('/home-section-load') }}/" + section.type, {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.text())
        .then(html => {
            target.innerHTML = html;
            reInitSwiper();

            setTimeout(function () {
                loadSection(index + 1);
            }, 400);
        })
        .catch(() => {
            target.innerHTML = '';
            loadSection(index + 1);
        });
    }

    setTimeout(function () {
        loadSection(0);
    }, 300);
});
</script>
@endpush