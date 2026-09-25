<div class="row g-2">
    @foreach($single_sub_category->products as $product)
        <div class="col-6 col-md-4 col-lg-3"> <!-- Mobile 2, Tablet 3, Desktop 4 -->
            <div class="product-card-wrapper">
                <div class="product-card">
                    @if($product->discount>0)
                    <div class="{{ $product->discount_type == 'percent' ? 'discount-badge' : 'discount-badge-flat' }}">
                        <span class="product-discount">{{ number_format($product->discount) }}</span>
                    </div>
                    @endif
                    <a class="z-2" href="{{ route('product.details', $product->id) }}">
                        <figure class="product-card-image ratio" style="--bs-aspect-ratio: 100%">
                            <img class="object-fit-contain product-img"
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
                                @if($product->discount>0)
                                <del>
                                    <span class="Price-amount">
                                        {{ number_format($product->regular_price) }} <span class="Price-currencySymbol">৳</span>
                                    </span>
                                </del>
                                @endif
                                <ins>
                                    <span class="Price-amount">
                                        {{ number_format($product->sale_price) }} <span class="Price-currencySymbol">৳</span>
                                    </span>
                                </ins>
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
    @endforeach
</div>

<style>
/* Mobile 2 per row, Tablet 3, Desktop 4 */
@media (max-width: 767.98px) {
    .product-card-wrapper {
        width: 100% !important; /* 2 per row */
    }
}
@media (min-width: 768px) and (max-width: 991.98px) {
    .product-card-wrapper {
        width: 100% !important; /* 3 per row */
    }
}
@media (min-width: 992px) {
    .product-card-wrapper {
        width: 100% !important; /* 4 per row */
    }
}
</style>