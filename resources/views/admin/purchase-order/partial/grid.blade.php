{{-- <option value="">-- Select Variant --</option>
@foreach ($variants as $item)
    <option value="{{ $item->id }}" data-price="{{ $item->purchase_price }}">
        @foreach ($item->values as $value)
            {{ ($value->attribute->name ?? '') . ' - ' . $value->name }}
            {{ !$loop->last ? ',' : '' }}
        @endforeach
    </option>
@endforeach --}}

@if ((isset($type) && $type == 'byCategory') || !isset($type))
    <div id="grid_breadcrumbs" class="py-2 border-bottom border-dashed">
        <a href="javascript:void(0);" class="px-2 category-link" data-category_id="0">All</a>
        @if (isset($breadcrumb))
            @foreach ($breadcrumb as $item)
                »
                <a href="javascript:void(0);" class="px-2 category-link" data-category_id="0">{{ $item->name }}</a>
            @endforeach
        @endif
    </div>
    <div class="row g-2 pt-2 pb-3 border-bottom border-dashed">
        @if ($category ?? null)
            <div class="col-auto">
                <div class="category-item get-back text-secondary" data-id="{{ $category->parent_id ?? null }}">
                    <div class="category-text truncate-text fs-11" style="--lines: 1;">« Go Back</div>
                </div>
            </div>
        @endif
        @foreach ($categories as $item)
            <div class="col-auto">
                <div class="category-item select-cat" data-id="{{ $item->id }}">
                    <div class="category-image">
                        <img src="{{ asset(file_exists($item->image) ? $item->image : 'category-placeholder.png') }}"
                            height="30" alt="{{ $item->name }}">
                    </div>
                    <div class="category-text truncate-text fs-11" style="--lines: 1;">{{ $item->name }}</div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row g-2 pt-3">
        @if (isset($products))
            @foreach ($products as $item)
                <div class="col-auto">
                    <div class="product-card">
                        <div class="product-card-image ratio ratio-4x3">
                            <img class="object-fit-cover"
                                src="{{ asset(file_exists($item->thumbnail) ? $item->thumbnail : 'placeholder.jpg') }}"
                                alt="{{ $item->name }}">
                        </div>
                        <div class="product-card-info">
                            <div class="product-card-text truncate-text fs-11" style="--lines: 1;">{{ $item->name }}
                            </div>
                            @php
                                $variant = $item->variants->where('status', true)->first();
                            @endphp
                            <div class="product-card-price">৳{{ number_format($variant->sale_price, 2) }}</div>
                            @if ($item->variants->where('status', true)->count() > 1)
                                <div class="variant-icon">
                                    <i class="far fa-plus"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endif

@if (isset($type) && $type == 'byTag')
    <div class="row g-2 pt-2 pb-3 border-bottom border-dashed">
        @foreach ($tags as $item)
            <div class="col-auto">
                <div class="category-item select-tag {{ ($tag ?? null) == $item->name ? 'active' : '' }}"
                    data-tag="{{ $item->name }}">
                    <div class="category-text truncate-text fs-11" style="--lines: 1;">{{ $item->name }}</div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row g-2 pt-3">
        @if (isset($products))
            @foreach ($products as $item)
                <div class="col-auto">
                    <div class="product-card">
                        <div class="product-card-image ratio ratio-4x3">
                            <img class="object-fit-cover"
                                src="{{ asset(file_exists($item->thumbnail) ? $item->thumbnail : 'placeholder.jpg') }}"
                                alt="{{ $item->name }}">
                        </div>
                        <div class="product-card-info">
                            <div class="product-card-text truncate-text fs-11" style="--lines: 1;">{{ $item->name }}
                            </div>
                            @php
                                $variant = $item->variants->where('status', true)->first();
                            @endphp
                            <div class="product-card-price">৳{{ number_format($variant->sale_price, 2) }}</div>
                            @if ($item->variants->where('status', true)->count() > 1)
                                <div class="variant-icon">
                                    <i class="far fa-plus"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endif

@if (isset($type) && $type == 'byVendor')
    <div class="row g-2 pt-2 pb-3 border-bottom border-dashed">
        @foreach ($vendors as $item)
            <div class="col-auto">
                <div class="category-item select-vendor {{ ($vendor_id ?? null) == $item->id ? 'active' : '' }}"
                    data-id="{{ $item->id }}">
                    <div class="category-text truncate-text fs-11" style="--lines: 1;">{{ $item->name }}</div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row g-2 pt-3">
        @if (isset($products))
            @foreach ($products as $item)
                <div class="col-auto">
                    <div class="product-card">
                        <div class="product-card-image ratio ratio-4x3">
                            <img class="object-fit-cover"
                                src="{{ asset(file_exists($item->thumbnail) ? $item->thumbnail : 'placeholder.jpg') }}"
                                alt="{{ $item->name }}">
                        </div>
                        <div class="product-card-info">
                            <div class="product-card-text truncate-text fs-11" style="--lines: 1;">{{ $item->name }}
                            </div>
                            @php
                                $variant = $item->variants->where('status', true)->first();
                            @endphp
                            <div class="product-card-price">৳{{ number_format($variant->sale_price, 2) }}</div>
                            @if ($item->variants->where('status', true)->count() > 1)
                                <div class="variant-icon">
                                    <i class="far fa-plus"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endif

@if (isset($type) && $type == 'byFavorite')
    <div class="row g-2 pt-3">
        @if (isset($products))
            @foreach ($products as $item)
                <div class="col-auto">
                    <div class="product-card">
                        <div class="product-card-image ratio ratio-4x3">
                            <img class="object-fit-cover"
                                src="{{ asset(file_exists($item->thumbnail) ? $item->thumbnail : 'placeholder.jpg') }}"
                                alt="{{ $item->name }}">
                        </div>
                        <div class="product-card-info">
                            <div class="product-card-text truncate-text fs-11" style="--lines: 1;">{{ $item->name }}
                            </div>
                            @php
                                $variant = $item->variants->where('status', true)->first();
                            @endphp
                            {{ info($variant) }}
                            <div class="product-card-price">৳{{ number_format($variant->sale_price, 2) }}</div>
                            @if ($item->variants->where('status', true)->count() > 1)
                                <div class="variant-icon">
                                    <i class="far fa-plus"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endif
