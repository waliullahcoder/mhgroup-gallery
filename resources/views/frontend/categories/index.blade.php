@extends('layouts.frontend.app')

@section('content')
 @if($subcategories->count()>0)
<div class="category-page py-4 animate__animated animate__fadeInLeft">
    <div class="container">
        <div class="row">

            <!-- LEFT SIDEBAR -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="filter-box">
                    <h5 class="filter-title">Filter</h5>
                    <!-- SUBJECT -->
                    @if($subcategories->count() > 0)  
                    <form id="filter-form"> 
                    <div class="filter-group">
                        <h6>Categories</h6>
                        <ul>
                            @foreach($subcategories as $sub)
                                <li>
                                    <label>
                                        <a href="{{route('category.singleCategoryPage', $sub->id)}}" class="section-link"> {{ $sub->name }}</a>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    </form>
                    @endif
                    @if($bookcat_count > 0)  
                    <!-- AUTHOR -->
                    <div class="filter-group">
                        <h6>Authors</h6>
                        <ul>
                            @foreach($authors ?? [] as $author)
                                <li>
                                    <label>
                                       <input type="checkbox" class="author-filter" value="{{ $author->id }}">
                                        {{ $author->name }}
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="filter-group">
                        <h6>Publications</h6>
                        <ul>
                            @foreach($publications ?? [] as $pub)
                                <li>
                                    <label>
                                        <input type="checkbox" 
                                            class="publication-filter"
                                            value="{{ $pub->id }}">
                                        {{ $pub->name }}
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                     <div class="filter-group">
                        <h6>Price Sort</h6>
                        <ul class="mb-3">
                            <li>
                                <label>
                                    <input type="radio" name="price_sort" value="low_high">
                                    Low to High
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="price_sort" value="high_low">
                                    High to Low
                                </label>
                            </li>
                        </ul>
                    </div>

                    <!-- PRICE -->
                    <div class="filter-group">
                        <h6>Price Range</h6>
                        <ul>
                            <li>
                                <label>
                                    <input type="checkbox" class="price-range-filter" value="0-200">
                                    0 – 200 Tk.
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" class="price-range-filter" value="201-500">
                                    201 – 500 Tk.
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" class="price-range-filter" value="500+">
                                    500+ Tk.
                                </label>
                            </li>
                        </ul>
                    </div>

                    @if($relatedProducts->count() > 0)
                    <div class="related-products mt-4">
                        <h6 class="mb-3">Others Related Products</h6>
                        <div class="row g-2">
                            @foreach($relatedProducts->take(8) as $product)
                                <div class="col-6">
                                    <div class="border p-2 h-100">

                                        <a href="{{ route('product.details', $product->id) }}" class="text-decoration-none text-dark">
                                            <img src="{{ asset($product->thumbnail) }}"
                                                class="img-fluid mb-2"
                                                alt="{{ $product->name }}">

                                            <small class="d-block text-truncate">
                                                {{ $product->name }}
                                            </small>

                                            <strong class="d-block">
                                                {{ number_format($product->sale_price ?? $product->regular_price) }} Tk.
                                            </strong>
                                        </a>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif


                </div>
            </div>
            <!-- RIGHT CONTENT -->

           
    <div class="col-lg-9">
    <div class="category-product-section pb-4">

        <div class="container">
             <div id="default-products">
                @include('frontend.categories.partials.product_list')
            </div>
            <div id="filtered-products"></div>
        </div>


    </div>
</div>

            <!-- END RIGHT CONTENT -->

        </div>
    </div>
</div>
@else
    @include('frontend.categories.partials.notfound')
@endif
@endsection
