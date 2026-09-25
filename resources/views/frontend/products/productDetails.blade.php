

@extends('layouts.frontend.app')

@section('content')
<style>
    .table{
        background:red !important;
    }
.small-thumb {
    transition: 0.3s;
    border: 2px solid transparent;
}

.small-thumb:hover {
    transform: scale(1.05);
}
.product-card img{
    width:20%
}
.gap-2{
    gap:0px !important;
}
.active-thumb {
    border: 2px solid #dc3545;
}

/*Zoom lens*/
#image-container{
    position:relative;
    display:inline-block;
}

#lens{
    position:absolute;
    background:rgba(255,255,255,.35);
    width:140px;
    height:140px;
    z-index:999;
    display:none;
    cursor:crosshair;
    pointer-events:none;
    box-shadow:0 0 8px rgba(0,0,0,.3);
}

#zoomResult{
    width:72%;
    height:600px;
     background:#1D363F;
    border:1px solid #ddd;
    background-repeat:no-repeat;
    display:none;
}
#zoomResult{
    position:absolute;
    top:0;
    left:50%;
    z-index:999999;
}
</style>
<div class="product-details-page py-4 animate__animated animate__fadeInDownBig">
    <div class="container">

        <!-- TOP SECTION -->
        <div class="row g-4">

            <!-- PRODUCT CARD (JS TARGET FOR FLYING IMAGE) -->
            <div class="col-lg-9">
                <div class="product-card">

                    <div class="row">
                     <!-- LEFT : PRODUCT IMAGE -->
                    <div class="col-lg-6">
                        <div class="border p-3">

                            <!-- MAIN IMAGE -->
                           

                            <!-- SMALL THUMBNAILS -->
                             <div class="row">
                        <div class="col-md-12">

                            <div class="position-relative d-inline-block" id="image-container">
                                <img id="productThumbnail"
                                    class="img-fluid border img-thumbnail small-thumb active-thumb product-img"
                                    src="{{ asset($product->thumbnail) }}"
                                    alt="{{ $product->name }}"
                                    style="cursor:pointer; max-height:500px; width:100%; object-fit:contain;">
                                  
                                     {{-- Extra Images --}}
                                @foreach($product->images->take(4) as $image)
                                    <img src="{{ asset($image->image) }}"
                                        class="img-thumbnail small-thumb"
                                        width="70"
                                        style="cursor:pointer;"
                                        onclick="changeImage(this)">
                                @endforeach

                                <div id="lens"></div>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div id="zoomResult"></div>

                        </div>
                    </div>
                           
                        </div>
                    </div>

                    
                    <script>
                    function changeImage(element) {

                        // Change main image
                        let mainImage = document.getElementById('productThumbnail');
                        mainImage.src = element.src;

                        // Change modal image also
                        document.getElementById('modalImage').src = element.src;

                        // Active border remove
                        document.querySelectorAll('.small-thumb').forEach(img => {
                            img.classList.remove('active-thumb');
                        });

                        // Active border add
                        element.classList.add('active-thumb');
                    }
                    </script>
                     <script>
                        //Zoom Lens
                   const image = document.getElementById("productThumbnail");
                    const lens = document.getElementById("lens");
                    const result = document.getElementById("zoomResult");

                    function initZoom(){

                        result.style.backgroundImage = `url('${image.src}')`;

                        const cx = result.offsetWidth / lens.offsetWidth;
                        const cy = result.offsetHeight / lens.offsetHeight;

                        result.style.backgroundSize =
                            (image.width * cx) + "px " +
                            (image.height * cy) + "px";

                        image.addEventListener("mousemove", moveLens);
                        lens.addEventListener("mousemove", moveLens);

                        image.addEventListener("mouseenter",()=>{
                            lens.style.display="block";
                            result.style.display="block";
                        });

                        image.addEventListener("mouseleave",()=>{
                            lens.style.display="none";
                            result.style.display="none";
                        });

                       function moveLens(e) {

                        const rect = image.getBoundingClientRect();

                        let x = e.clientX - rect.left;
                        let y = e.clientY - rect.top;

                        // Lens half size
                        x = x - lens.offsetWidth / 2;
                        y = y - lens.offsetHeight / 2;

                        if (x < 0) x = 0;
                        if (y < 0) y = 0;

                        if (x > image.clientWidth - lens.offsetWidth)
                            x = image.clientWidth - lens.offsetWidth;

                        if (y > image.clientHeight - lens.offsetHeight)
                            y = image.clientHeight - lens.offsetHeight;

                        lens.style.left = x + "px";
                        lens.style.top = y + "px";

                        // Original image scale
                        let scaleX = image.naturalWidth / image.clientWidth;
                        let scaleY = image.naturalHeight / image.clientHeight;

                        result.style.backgroundSize =
                            (image.naturalWidth) + "px " +
                            (image.naturalHeight) + "px";

                        result.style.backgroundPosition =
                            "-" + (x * scaleX) + "px -" + (y * scaleY) + "px";
                    }
                    }

                    initZoom();

                    function changeImage(el){

                        image.src = el.src;

                        setTimeout(function(){

                            result.style.backgroundImage=`url('${image.src}')`;

                            const cx = result.offsetWidth / lens.offsetWidth;
                            const cy = result.offsetHeight / lens.offsetHeight;

                            result.style.backgroundSize =
                                (image.width * cx) + "px " +
                                (image.height * cy) + "px";

                        },100);

                    }
                    </script> 


                        <!-- RIGHT : PRODUCT DETAILS -->
                        <div class="col-lg-6">
                            <div class="p-3">

                                <h2 class="mb-2">{{ $product->name }}</h2>

                                <!-- BADGES -->
                                <div class="mb-2">
                                    @if($product->best_seller)
                                        <span class="badge bg-success">Best Seller</span>
                                    @endif
                                    @if($product->new_arrival)
                                        <span class="badge bg-primary">New Arrival</span>
                                    @endif
                                    @if($product->trending)
                                        <span class="badge bg-warning text-dark">Trending</span>
                                    @endif
                                </div>

                                <!-- RATING -->
                                @php
                                    $avgRating   = round($product->averageRating(), 1); // e.g. 4.5
                                    $reviewCount = $product->reviews->count();          // total users
                                @endphp

                                <div class="mb-2 text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($avgRating))
                                            ★
                                        @elseif($i - $avgRating < 1)
                                            ☆
                                        @else
                                            ☆
                                        @endif
                                    @endfor

                                    <span class="text-muted">
                                        ({{ $avgRating > 0 ? $avgRating : '0.0' }} / 5
                                        · {{ $reviewCount }} {{ $reviewCount == 1 ? 'review' : 'reviews' }})
                                    </span>
                                </div>


                                <!-- SHORT DESCRIPTION -->
                                <!-- <div class="mb-3 text-muted">
                                    {!! $product->short_description !!}
                                </div> -->

                                <!-- PRICE -->
                                <div class="mb-4">
                                        <h3 class="text-danger">
                                            {{ number_format($product->sale_price, 2) }} ৳
                                            @if($product->discount>0)
                                            <del class="fs-6 text-muted ms-2">
                                                {{ number_format($product->regular_price, 2) }} ৳
                                            </del>
                                            @endif
                                        </h3>
                                </div>

                                <!-- ACTION BUTTONS -->
                                <div class="d-flex gap-2 mb-4" style="gap:0.5rem !important">
                                    <button class="btn btn-sm btn-danger add-to-cart"
                                            data-id="{{ $product->id }}">
                                        Add +
                                    </button>
                                   
                                    @php
                                        $alreadyWishlisted = auth()->check() &&
                                            auth()->user()->wishlists()->where('product_id', $product->id)->exists();
                                    @endphp
                                    @if($alreadyWishlisted)
                                        <button class="btn btn-sm btn-danger" disabled>
                                            ❤️ Wishlisted
                                        </button>
                                    @else
                                        <form action="{{ route('wishlist.store', $product->id) }}"
                                            method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-danger"{{$product->variants->sum('stock')>0 ? '' : 'disabled'}}>
                                                🤍 Add to Wishlist
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('cart.buyNow') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="variant_id" value="">
                                        <button type="submit" class="btn btn-sm btn-warning">
                                            Buy Now
                                        </button>
                                    </form>
                                    <a href="https://wa.me/8801575020231?text=Hello%20I%20want%20to%20contact%20you" target="_blank" class="btn btn-sm btn-success">
                                           Whatsapp Us
                                    </a>

                                </div>

                                <!-- META INFO -->
                                 <div style="font-size: 14px; line-height: 1.8;">

                                <p style="margin: 0 0 8px;text-align:left !important;">
                                    <strong style="color: #ff9c00;">
                                        ✔ ১০০% ক্যাশ অন ডেলিভারি (হাতে পণ্য পেয়ে মূল্য পরিশোধ করুন)।
                                    </strong>
                                </p>

                                <p style="margin: 0 0 8px;text-align:left !important;">
                                    <strong style="color: #ff9c00;">
                                        ✔ ঢাকার ভিতরে ১ দিন আর ঢাকার বাহিরে ৩ দিনে পণ্য হাতে পাবেন।
                                    </strong>
                                </p>

                                <p style="margin: 0 0 8px;text-align:left !important;">
                                    <strong style="color: #ff9c00;">
                                        ✔ অর্ডার করতে Buy Now এ ক্লিক করুন।
                                    </strong>
                                </p>

                                <p style="margin: 0 0 8px;text-align:left !important;">
                                    <strong style="color: #ff9c00;">
                                        ✔ আপনার নাম, মোবাইল নং এবং ঠিকানা সঠিকভাবে দিন।
                                    </strong>
                                </p>

                                <p style="margin: 0 0 8px;text-align:left !important;">
                                    <strong style="color: #ff9c00;">
                                        ✔ প্রি-অর্ডার পদ্ধতিতে নিশ্চিত ডিস্কাউন্ট সুবিধা গ্রহণ করুন।
                                    </strong>
                                </p>

                                <p style="margin: 0 0 8px;text-align:left !important;">
                                    <strong style="color: #ff9c00;">
                                        ✔ পণ্য ফেরত দেওয়ার ক্ষেত্রে রিটার্ন পলিসি দেখুন।
                                    </strong>
                                </p>

                                <p style="margin: 0 0 8px;text-align:left !important;">
                                    <strong style="color: #ff9c00;">
                                        ✔ অর্ডার করার সময় প্রয়োজনীয় তথ্য সঠিকভাবে প্রদান করুন।
                                    </strong>
                                </p>

                                <p style="margin: 0 0 8px;text-align:left !important;">
                                    <strong style="color: #ff9c00;">
                                        ✔ ডেলিভারির সময় পণ্যটি ভালোভাবে যাচাই করে গ্রহণ করুন।
                                    </strong>
                                </p>

                                <p style="margin: 0;text-align:left !important;">
                                    <strong style="color: #ff9c00;">
                                        ✔ আপনার সন্তুষ্টিই আমাদের প্রধান লক্ষ্য—যেকোনো সমস্যায় আমাদের সাপোর্ট টিমের সাথে যোগাযোগ করুন।
                                    </strong>
                                </p>

                            </div>
                               

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- RELATED PRODUCTS -->
            <div class="col-lg-3">
                <div class="p-3">
                    <h6 class="mb-3">Related Products</h6>

                    @foreach($relatedProducts ?? [] as $item)
                        <div class="border d-flex mb-3" style="margin-top:10px;">
                            <img src="{{ asset($item->thumbnail) }}"
                                 class="me-2"
                                 width="60"
                                 alt="">
                            <div>
                                <a href="{{ route('product.details', $item->id) }}"
                                   class="small fw-semibold d-block">
                                   {{ \Illuminate\Support\Str::limit($item->name, 30) }}
                                </a>
                                <p>{{ $item->code }}</p>
                                <span class="text-danger">
                                    {{ number_format($item->sale_price ?? $item->regular_price, 2) }} ৳
                                </span>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

        </div>
        <!-- END TOP -->

        <!-- BOTTOM SECTION -->
        <div class="row mt-5">
            <div class="col-12">

                <div class="bg-white border">

                    <!-- TABS -->
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#description">
                                Description
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#information">
                                Information
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#reviews">
                                Reviews ({{ $product->reviews->count() }})
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content p-4">

                        <div class="tab-pane fade show active" id="description">
                            {!! $product->description !!}
                        </div>

                        <div class="tab-pane fade" id="information">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Category</th>
                                    <td>{{ $product->category->name??'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Authors</th>
                                    <td>{{ $product->authors->pluck('name')->implode(', ') }}</td>
                                </tr>
                                <tr>
                                    <th>Publication</th>
                                    <td>{{ $product->publication->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Barcode</th>
                                    <td>{{ $product->barcode }}</td>
                                </tr>
                                <tr>
                                    <th>Editor</th>
                                    <td>{{ $product->uom->name??'N/A' }}</td>
                                </tr>
                            </table>
                        </div>

                        

                            {{-- REVIEW LIST --}}
                        <div class="tab-pane fade" id="reviews">
                            <h6 class="mb-3">
                                Customer Reviews ({{ $product->reviews->count() }})
                            </h6>
                            @forelse($product->reviews as $review)
                                <div class="border-bottom pb-2 mb-3">
                                    <strong>{{ $review->user->name }}</strong>

                                    <div class="text-warning">
                                        @for($i=1;$i<=5;$i++)
                                            {{ $i <= $review->rating ? '★' : '☆' }}
                                        @endfor
                                    </div>

                                    <p class="mb-0 text-muted">
                                        {{ $review->review }}
                                    </p>
                                </div>
                            @empty
                                <p class="text-muted">No reviews yet.</p>
                            @endforelse

                            {{-- REVIEW FORM --}}
                            @auth
                                @if($review_count == 0)
                                <hr>
                                <h6>Write a Review</h6>

                                <form method="POST" action="{{ route('review.store', $product->id) }}">
                                    @csrf

                                    <div class="mb-2">
                                        <label class="form-label">Rating</label>
                                        <select name="rating" class="form-select" required>
                                            <option value="">Select Rating</option>
                                            @for($i=5;$i>=1;$i--)
                                                <option value="{{ $i }}">{{ $i }} Star</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Review</label>
                                        <textarea name="review" rows="3"
                                                class="form-control"
                                                placeholder="Write your experience..."></textarea>
                                    </div>

                                    <button class="btn btn-primary btn-sm">
                                        Submit Review
                                    </button>
                                </form>
                                @endif
                            @else
                                <p class="text-muted mt-3">
                                    Please <a href="{{ route('login') }}">login</a> to write a review.
                                </p>
                            @endauth
                        </div>

                            {{-- REVIEW LIST \\\\--}}


                            
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>
@endsection
