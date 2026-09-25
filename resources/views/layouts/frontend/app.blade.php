<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}" />

<title>{{ $settings->meta_name ?? 'MH Gallery - Smart Ecommerce & All type of products Platform' }}</title>

<meta name="keywords" content="{{ $settings->meta_keywords ?? 'MH Gallery, ecommerce platform Bangladesh, POS system, inventory management, online shop software, business management system, MH Gallery marketplace' }}">

<meta name="description" content="{{ $settings->meta_description ?? 'MH Gallery is a powerful ecommerce and business management platform designed to manage products, inventory, sales, invoices, and customers easily. Grow your business smarter with MH Gallery.' }}">

<meta property="og:title" content="{{ $settings->meta_name ?? 'MH Gallery - Smart Ecommerce Platform' }}">
<meta property="og:description" content="{{ $settings->meta_description ?? 'Manage your business, products, inventory, and sales easily with MH Gallery ecommerce platform.' }}">
<meta property="og:image" content="{{ asset($settings->meta_image ?? 'frontend/images/logo/logo.png') }}">
<meta property="og:type" content="website">

<link rel="shortcut icon"
    href="{{ asset(file_exists($settings->favicon) ? $settings->favicon : 'frontend/images/logo/favicon.png') }}"
    type="image/x-icon">
    @include('layouts.frontend.partial.styles')
</head>

<body style="background: #545252">
    @include('layouts.frontend.partial.header')
    @yield('content')
    <script type="text/javascript" src="{{ asset('frontend/js/sweetalert2@11.js') }}"></script>
    @include('layouts.frontend.partial.alert')
    @include('layouts.frontend.partial.footer')
    @include('layouts.frontend.partial.scripts')
    {{-- <script type="text/javascript" src="{{ asset('frontend/js/jquery-3.6.0.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

{{-- Cat page for filterleft side --}}
<script>
$(document).ready(function(){

    $(document).on('change', '.publication-filter, .author-filter, input[name="price_sort"], .price-range-filter', function(){

        let publications = [];
        let authors = [];
        let priceSort = $('input[name="price_sort"]:checked').val() || null;
        let priceRanges = [];

        $('.publication-filter:checked').each(function(){
            publications.push($(this).val());
        });

        $('.author-filter:checked').each(function(){
            authors.push($(this).val());
        });

         $('.price-range-filter:checked').each(function(){
            priceRanges.push($(this).val());
        });


        // যদি যেকোনো একটাও checked থাকে
        if(publications.length > 0 || authors.length > 0 || priceSort != null || priceRanges.length > 0){

            $.ajax({
                url: "{{ route('filter.products') }}",
                type: "GET",
                data: { 
                    publications: publications,
                    authors: authors,
                    price_sort: priceSort,
                    price_ranges: priceRanges
                },
                success: function(response){
                    $('#default-products').hide();
                    $('#filtered-products').html(response).show();
                }
            });

        }else{

            // সব unchecked হলে default show
            $('#filtered-products').hide().html('');
            $('#default-products').show();
        }

    });

});
</script>


{{--Sub Cat page for filterleft side --}}
<script>
$(document).ready(function(){

    $(document).on('change', '.publication-filter-sub, .author-filter-sub, .author-filter-sub, input[name="price_sort_sub"], .price-range-filter-sub', function(){

        let publications = [];
        let authors = [];
        let priceSort = $('input[name="price_sort_sub"]:checked').val() || null;
        let priceRanges = [];

        $('.publication-filter-sub:checked').each(function(){
            publications.push($(this).val());
        });

        $('.author-filter-sub:checked').each(function(){
            authors.push($(this).val());
        });

         $('.price-range-filter-sub:checked').each(function(){
            priceRanges.push($(this).val());
        });


        // যদি যেকোনো একটাও checked থাকে
        if(publications.length > 0 || authors.length > 0 || priceSort != null || priceRanges.length > 0){

            $.ajax({
                url: "{{ route('filter.sub.products') }}",
                type: "GET",
                data: { 
                    publications: publications,
                    authors: authors,
                    price_sort: priceSort,
                    price_ranges: priceRanges
                },
                success: function(response){
                    $('#default-products-sub').hide();
                    $('#filtered-products-sub').html(response).show();
                }
            });

        }else{

            // সব unchecked হলে default show
            $('#filtered-products-sub').hide().html('');
            $('#default-products-sub').show();
        }

    });

});
</script>

    {{-- // AJAX Live Search --}}
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<script>
$(document).ready(function(){

    //Desktop Search
    $('#product-search-input').on('keyup', function(){
        let query = $(this).val();
        if(query.length >= 2) {
            $.ajax({
                url: "{{ route('products.search') }}",
                method: "GET",
                data: { query: query },
                success: function(data) {
                    let html = '';
                    if(data.length > 0){
                        html += '<ul class="list-group list-group-flush">';
                        data.forEach(function(item){
                            html += `<li class="list-group-item p-2 d-flex justify-content-between align-items-center product-card">
                                        <a href="/product/details/${item.id}" class="d-flex align-items-center gap-2 text-decoration-none text-dark flex-grow-1">
                                            <img class="product-img" src="${item.thumbnail}" style="position:relative; z-index:999;width:50px; height:50px; border-radius:10px;" alt="${item.name}">
                                            <div>
                                                <strong>${item.name}</strong><br>
                                                <small>${item.authors}</small>
                                            </div>
                                        </a>
                                        <div class="text-end ms-3">
                                            <div><del>৳${item.regular_price}</del> <strong>৳${item.sale_price}</strong></div>
                                            <button class="btn btn-sm btn-primary mt-1 add-to-cart-btn add-to-cart" data-id="${item.id}">Add +</button>
                                        </div>
                                    </li>`;
                        });
                        html += '</ul>';
                        $('#search-results').html(html).fadeIn();
                    } else {
                        $('#search-results').html('<div class="p-2">কোন প্রোডাক্ট পাওয়া যায়নি</div>').fadeIn();
                    }
                }
            });
        } else {
            $('#search-results').fadeOut();
        }
    });

    // Click outside to hide results
    $(document).on('click', function(e){
        if(!$(e.target).closest('.search-area').length){
            $('#search-results').fadeOut();
        }
    });


    //Mobile Search
    $('#mobile-product-search-input').on('keyup', function(){
        let query = $(this).val();
        if(query.length >= 2) {
            $.ajax({
                url: "{{ route('products.search') }}",
                method: "GET",
                data: { query: query },
                success: function(data) {
                    let html = '';
                    if(data.length > 0){
                        html += '<ul class="list-group list-group-flush">';
                        data.forEach(function(item){
                            html += `<li class="list-group-item p-2 d-flex justify-content-between align-items-center product-card">
                                        <a href="/product/details/${item.id}" class="d-flex align-items-center gap-2 text-decoration-none text-dark flex-grow-1">
                                            <img class="product-img" src="${item.thumbnail}" style="position:relative; z-index:999;width:50px; height:50px; border-radius:10px;" alt="${item.name}">
                                            <div>
                                                <strong>${item.name}</strong><br>
                                                <small>${item.authors}</small>
                                            </div>
                                        </a>
                                        <div class="text-end ms-3">
                                            <div><del>৳${item.regular_price}</del> <strong>৳${item.sale_price}</strong></div>
                                            <button class="btn btn-sm btn-primary mt-1 add-to-cart-btn add-to-cart" data-id="${item.id}">+</button>
                                        </div>
                                    </li>`;
                        });
                        html += '</ul>';
                        $('#mobile-search-results').html(html).fadeIn();
                    } else {
                        $('#mobile-search-results').html('<div class="p-2">কোন প্রোডাক্ট পাওয়া যায়নি</div>').fadeIn();
                    }
                }
            });
        } else {
            $('#mobile-search-results').fadeOut();
        }
    });

    // Click outside to hide results
    $(document).on('click', function(e){
        if(!$(e.target).closest('.mobile-search-area').length){
            $('#mobile-search-results').fadeOut();
        }
    });

    // Add + button click
    $(document).on('click', '.add-to-cart-btn', function(){
        let productId = $(this).data('id');
        // AJAX call to add product to cart
        $.ajax({
            url: '/cart/add',
            method: 'POST',
            data: {
                product_id: productId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response){
                alert('Product added to cart!');
                // Optionally, update cart count here
            }
        });
    });

});
</script>

{{-- Add + --}}
<script>
$(document).on('click', '.add-to-cart', function(e) {

    e.preventDefault();

    let button = $(this);

    let productId = button.data('id');

    // IMPORTANT: HTML এ data-variant-id হলে এখানে এভাবেই নিতে হবে
    let variantId = button.attr('data-variant-id');

    let image = button.closest('.product-card').find('.product-img');

    let cart = $('.cart-icon');


    console.log('Product ID:', productId);
    console.log('Variant ID:', variantId);


    // Variant অবশ্যই থাকতে হবে
    if (!variantId) {
        alert('Please select a variant first.');
        return;
    }


    // Flying image
    if (image.length && cart.length) {

        let flyingImg = image.clone()
            .css({
                position: 'absolute',
                zIndex: 9999,
                width: image.width(),
                top: image.offset().top,
                left: image.offset().left
            })
            .appendTo('body');


        flyingImg.animate({
            top: cart.offset().top,
            left: cart.offset().left,
            width: 20,
            opacity: 0.5
        }, 700, function () {

            flyingImg.remove();

        });

    }


    // Add to cart
    $.ajax({

        url: "{{ route('cart.add') }}",

        method: "POST",

        data: {

            _token: "{{ csrf_token() }}",

            product_id: productId,

            variant_id: variantId

        },

        success: function(res) {

            $('.cart-count').text(res.count);

            console.log('Added:', res);

        },

        error: function(xhr) {

            console.log(xhr.responseText);

            alert('Unable to add product to cart.');

        }

    });

});
</script>

{{-- Cart update --}}
@php
    $tax = $settings->tax/100;
    $discount = $settings->discount;
    $discount_type = $settings->discount_type;
@endphp
<script>  
$(document).ready(function () {

    function calculateCart() {
        let subtotal = 0;
    // Inject PHP values into JS
    const TAX_RATE = {{ $tax }};
    const DISCOUNT_RATE = {{ $discount }};
    const DISCOUNT_TYPE = '{{ $discount_type }}';


        $('.cart-item').each(function () {
            let price = parseFloat($(this).data('price'));
            let qty   = parseInt($(this).find('.qty').val());
            subtotal += price * qty;
        });

         // ✅ discount calculation
        let discount = 0;
        if(DISCOUNT_TYPE === 'percent') {
            discount = subtotal * (DISCOUNT_RATE / 100);
        } else if(DISCOUNT_TYPE === 'amount') {
            discount = DISCOUNT_RATE;
        }

        let tax      = subtotal * TAX_RATE;
        let total    = subtotal - discount + tax;

        $('#subtotal').text('৳' + subtotal.toFixed(2));
        $('#discount').text('- ৳' + discount.toFixed(2));
        $('#tax').text('৳' + tax.toFixed(2));
        $('#total').text('৳' + total.toFixed(2));
    }

    calculateCart();

    $(document).on('click', '.qty-plus, .qty-minus', function () {

        let card = $(this).closest('.cart-item');
        let qtyInput = card.find('.qty');
        let qty = parseInt(qtyInput.val());
        let price = parseFloat(card.data('price'));

        if ($(this).hasClass('qty-plus')) {
            qty++;
        } else {
            qty = Math.max(1, qty - 1);
        }

        qtyInput.val(qty);

        let itemTotal = price * qty;
        card.find('.item-subtotal').text('৳' + itemTotal.toFixed(2));

        $.ajax({
            url: "{{ route('cart.update') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: card.data('id'),
                qty: qty
            }
        });

        calculateCart();
    });

});
</script>
</body>

</html>
