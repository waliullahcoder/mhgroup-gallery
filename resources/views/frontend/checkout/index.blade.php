@extends('layouts.frontend.app')

@section('content')
@include('frontend.checkout.style')
<div class="container py-5">
    <form action="{{ route('checkout.placeOrder') }}" method="POST">
        @csrf

        <div class="row">

            {{-- LEFT : CART / INVOICE --}}
            <div class="col-lg-7">
            <div class="order-summary-card mb-4">
                <div class="order-summary-header">
                    <div>
                        <h4>🧾 Order Summary</h4>
                        <p>আপনার অর্ডারের বিস্তারিত</p>
                    </div>
                    <span class="item-count">{{ count($cart) }} Items</span>
                </div>

                <div class="order-table-wrap">
                    <table class="table order-table align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Size/Specific</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php $subtotal = 0; @endphp

                            @foreach($cart as $item)
                                @php
                                    $line = $item['price'] * $item['qty'];
                                    $subtotal += $line;
                                @endphp

                                <tr>
                                    <td>
                                        <div class="product-name">
                                            {{ $item['name'] }}
                                        </div>
                                    </td>
                                     <td>
                                    @php
                                        $type = $item['product_type'] ?? '';
                                    @endphp

                                    @if($type == 'mirror')

                                        {{-- Mirror Size --}}
                                        <div class="mt-2">
                                            <div class="row g-2">
                                                <div class="col-12">
                                                     <select name="size[{{ $item['id'] }}]" class="form-select form-select-sm">
                                                        <option value="">Select Size</option>
                                                        @for($height = 18; $height <= 72; $height += 6)
                                                            @for($width = 18; $width <= 72; $width += 6)
                                                                <option value="{{ $height }}X{{ $width }}">
                                                                    {{ $height }}" X {{ $width }}"
                                                                </option>
                                                            @endfor
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    @elseif($type == 'cloths')

                                        {{-- Cloth Size --}}
                                        <div class="mt-2">
                                           <select name="size[{{ $item['id'] }}]" class="form-select form-select-sm">
                                                <option value="">Select Size</option>
                                                <option value="S">Small (S)</option>
                                                <option value="M">Medium (M)</option>
                                                <option value="L">Large (L)</option>
                                                <option value="XL">Extra Large (XL)</option>
                                                <option value="XXL">Double Extra Large (XXL)</option>
                                            </select>
                                        </div>

                                    @elseif($type == 'earrings')

                                        {{-- Ornament Size --}}
                                        <div class="mt-2">
                                            <select name="size[{{ $item['id'] }}]" class="form-select form-select-sm">
                                                <option value="">Select Size</option>
                                                <option value="Free Size">Free Size</option>
                                                <option value="Small">Small</option>
                                                <option value="Medium">Medium</option>
                                                <option value="Large">Large</option>
                                            </select>
                                        </div>
                                    @elseif($type == 'bangles')

                                        {{-- Ornament Size --}}
                                        <div class="mt-2">
                                           <select name="size[{{ $item['id'] }}]" class="form-select form-select-sm">
                                                <option value="">Select Size</option>
                                                <option value="2.2">2.2 (Small)</option>
                                                <option value="2.4">2.4 (Medium)</option>
                                                <option value="2.6">2.6 (Large)</option>
                                                <option value="2.8">2.8 (XL)</option>
                                                <option value="2.10">2.10 (XXL)</option>
                                            </select>
                                        </div>
                                    @elseif($type == 'anklet')

                                        {{-- Ornament Size --}}
                                        <div class="mt-2">
                                          <select name="size[{{ $item['id'] }}]" class="form-select form-select-sm">
                                                <option value="">Select Size</option>
                                                <option value="6 inch">6 Inch (Small)</option>
                                                <option value="7 inch">7 Inch (Medium)</option>
                                                <option value="8 inch">8 Inch (Large)</option>
                                                <option value="9 inch">9 Inch (XL)</option>
                                                <option value="10 inch">10 Inch (XXL)</option>
                                            </select>
                                        </div>
                                    @elseif($type == 'bracelet')

                                        {{-- Ornament Size --}}
                                        <div class="mt-2">
                                         <select name="size[{{ $item['id'] }}]" class="form-select form-select-sm">
                                                <option value="">Select Size</option>
                                                <option value="15 cm">15 cm</option>
                                                <option value="16 cm">16 cm</option>
                                                <option value="17 cm">17 cm</option>
                                                <option value="18 cm">18 cm</option>
                                                <option value="19 cm">19 cm</option>
                                                <option value="20 cm">20 cm</option>
                                                <option value="21 cm">21 cm</option>
                                            </select>
                                        </div>
                                    @elseif($type == 'ring')

                                        {{-- Ornament Size --}}
                                        <div class="mt-2">
                                         <select name="size[{{ $item['id'] }}]" class="form-select form-select-sm">
                                                <option value="">Select Ring Size</option>
                                                <option value="US 5">US 5</option>
                                                <option value="US 6">US 6</option>
                                                <option value="US 7">US 7</option>
                                                <option value="US 8">US 8</option>
                                                <option value="US 9">US 9</option>
                                                <option value="US 10">US 10</option>
                                                <option value="US 11">US 11</option>
                                                <option value="US 12">US 12</option>
                                            </select>
                                        </div>
                                    @elseif($type == 'necklace')

                                        {{-- Ornament Size --}}
                                        <div class="mt-2">
                                        <select name="size[{{ $item['id'] }}]" class="form-select form-select-sm">
                                                <option value="">Select Necklace Size</option>
                                                <option value="14 inch">14 Inch (Choker)</option>
                                                <option value="16 inch">16 Inch</option>
                                                <option value="18 inch">18 Inch (Most Popular)</option>
                                                <option value="20 inch">20 Inch</option>
                                                <option value="22 inch">22 Inch</option>
                                                <option value="24 inch">24 Inch</option>
                                                <option value="30 inch">30 Inch (Long Necklace)</option>
                                            </select>
                                        </div>
                                    @elseif($type == 'bra')

                                        {{-- Ornament Size --}}
                                        <div class="mt-2">
                                        <select name="size[{{ $item['id'] }}]" class="form-select form-select-sm">
                                                <option value="">Select Bra Size</option>
                                                <option value="32">32</option>
                                                <option value="34">34</option>
                                                <option value="36">36</option>
                                                <option value="38">38</option>
                                                <option value="40">40</option>
                                                <option value="42">42</option>
                                            </select>
                                        </div>

                                    @elseif($type == 'fragrance')

                                        {{-- Fragrance Volume --}}
                                        <div class="mt-2">
                                            <select name="size[{{ $item['id'] }}]" class="form-select form-select-sm">
                                                <option value="">Select Volume</option>
                                                @for($ml = 10; $ml <= 100; $ml += 10)
                                                    <option value="{{ $ml }}ML">{{ $ml }} ML</option>
                                                @endfor
                                            </select>
                                        </div>
                                     @elseif($type == 'other')

                                        {{-- Fragrance Volume --}}
                                        <!-- <div class="mt-2">
                                            <input type="text" name="size[{{ $item['id'] }}]" class="form-control">
                                        </div> -->
                                        -

                                    @endif
                                </td>
                                    <td class="text-center">
                                        <span class="qty-badge">{{ $item['qty'] }}</span>
                                    </td>
                                    <td class="text-end">
                                        ৳{{ number_format($item['price'],2) }}
                                    </td>
                                    <td class="text-end fw-bold">
                                        ৳{{ number_format($line,2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

               @php
                    $discount = (
                        $settings->discount_type=='percent'
                        ? $subtotal * $settings->discount/100
                        : $settings->discount
                    );

                    $tax = $subtotal * $settings->tax/100;

                    $delivery = 80; // default dhaka

                    $total = $subtotal - $discount + $tax + $delivery;
                @endphp

                <input type="hidden" name="subtotal" value="{{ $subtotal }}">
                <input type="hidden" id="discount_input" name="discount" value="{{ $discount }}">
                <input type="hidden" name="tax" value="{{ $tax }}">
                <input type="hidden" id="delivery_charge" name="delivery_charge" value="80">
                <input type="hidden" id="final_total" name="total" value="{{ $total }}">


                <div class="summary-total-box">

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <strong>৳{{ number_format($subtotal,2) }}</strong>
                    </div>

                    <div class="summary-row">
                        <div class="mb-3">
                            <label class="form-check-label fw-bold">
                                <input type="checkbox"
                                    class="form-check-input me-2"
                                    id="preorder">

                                Pre Order Discount Apply
                                {{ $settings->discount_type=='percent'
                                    ? '('.$settings->discount.'%)'
                                    : '' }}
                            </label>
                        </div>

                        
                        
                        <strong class="text-danger" id="discountAmount">
                            -৳{{ number_format($discount,2) }}
                        </strong>
                    </div>

                    <div id="preorderBox" class="mt-3" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <span class="text-danger">*</span>(প্রি-অর্ডার ডিসকাউন্ট  সুবিধা পেতে চাইলে আগে পেমেন্ট করুন এবং পণ্যটি গ্রহন করতে ১০ দিন ডেলিভারির জন্য অপেক্ষা করুন।  Transaction ID  ভুল প্রবেশ করলে আপনার অর্ডার গ্রহন করা হবে না।)
                                </label>

                                <input type="text"
                                    class="form-control"
                                    id="transaction_id"
                                    name="transaction_id"
                                    placeholder="প্রি-অর্ডার ট্রাঞ্জেকশন আইডি প্রবেশ করুন এইখানে and then click on 🛒 Place Order Button ">
                            </div>

                           <button type="button" class="pay-btn border-0" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                    এখনই পেমেন্ট করুন
                                    <span>Pay now</span>
                                </button>
                        </div>

                    <div class="summary-row">
                        <span>Tax ({{$settings->tax}}%)</span>
                        <strong>৳{{ number_format($tax,2) }}</strong>
                    </div>


                    {{-- Delivery Charge --}}

                    <div class="delivery-box">

                        <h6 class="delivery-title">
                            🚚 Delivery Charge
                        </h6>

                        <label class="delivery-option active">

                            <input
                                type="radio"
                                name="delivery_type"
                                value="80"
                                checked>

                            <span class="radio-ui"></span>

                            <div>
                                <strong>ঢাকার ভিতরে</strong>
                                <small>Delivery Charge</small>
                            </div>

                            <span class="delivery-price">
                                ৳80
                            </span>

                        </label>


                        <label class="delivery-option">

                            <input
                                type="radio"
                                name="delivery_type"
                                value="120">

                            <span class="radio-ui"></span>

                            <div>
                                <strong>ঢাকার বাইরে</strong>
                                <small>Delivery Charge</small>
                            </div>

                            <span class="delivery-price">
                                ৳120
                            </span>

                        </label>
                         <label class="delivery-option">

                            <input
                                type="radio"
                                name="delivery_type"
                                value="350">

                            <span class="radio-ui"></span>

                            <div>
                                <strong>ঢাকার ভিতরে (Only LED Mirror)</strong>
                                <small>Delivery Charge</small>
                            </div>

                            <span class="delivery-price">
                                ৳350
                            </span>

                        </label>

                        <label class="delivery-option">

                            <input
                                type="radio"
                                name="delivery_type"
                                value="1500">

                            <span class="radio-ui"></span>

                            <div>
                                <strong>ঢাকার বাইরে (Only LED Mirror)</strong>
                                <small>Delivery Charge</small>
                            </div>

                            <span class="delivery-price">
                                ৳1500
                            </span>

                        </label>

                    </div>


                    <div class="summary-row">
                        <span>Delivery Charge</span>

                        <strong id="delivery_amount">
                            ৳80.00
                        </strong>
                    </div>


                    <div class="summary-divider"></div>

                    <div class="summary-row grand-total">
                        <span>Total Payable</span>

                        <strong id="grandTotal">
                            ৳{{ number_format($total,2) }}
                        </strong>
                    </div>

                </div>
            </div>
        </div>

            {{-- RIGHT : CUSTOMER + PAYMENT --}}
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body">

                        {{-- CUSTOMER INFO --}}
                        @if(!auth()->check())
                            <h5 class="mb-3">👤 Customer Information</h5>

                            <div class="mb-2" style="margin-top:10px;">
                                <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                            </div>

                            <div class="mb-2">
                                <input type="number" name="phone" class="form-control" placeholder="Mobile No." required>
                            </div>
                            <div class="mb-2">
                                <input type="text" name="address" class="form-control" placeholder="Delivery Address" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>
                            
                            <p>If you have an account, please continue to <a href="{{ route('auth.signinPage') }}" style="color:green">Login</a></p>
                        @endif
                        
                        

                        {{-- PAYMENT METHOD --}}
                        <div class="payment-section">
                            <div class="mb-2" style="margin-top:10px;">
                                <input type="text" name="note" class="form-control" placeholder="(Note) write here something if need..">
                            </div>
                            <h5 class="payment-heading">💳 Payment Method</h5>

                            <div class="checkout-box">
                                <div class="checkout-title">
                                    পেমেন্ট পদ্ধতি বেছে নিন
                                    <span>Choose payment method</span>
                                </div>

                                
                                <label class="method-option">
                                    <input type="radio" name="payment_method" value="cod" checked>
                                    <span class="radio-ui"></span>
                                    <span class="method-icon cod">✓</span>
                                    <span class="method-info">
                                        <strong>Cash on Delivery</strong>
                                        <small>পণ্য হাতে পেলে টাকা দিন</small>
                                    </span>
                                </label>

                                <label class="method-option selected">
                                    <input type="radio" name="payment_method" value="bkash">
                                    <span class="radio-ui"></span>
                                    <span class="method-icon bkash">৳</span>
                                    <span class="method-info">
                                        <strong>bKash <span style="font-size:12px;">01916304877(Personal)</span></strong>
                                        <small>মোবাইল ব্যাংকিং — সবচেয়ে জনপ্রিয়</small>
                                    </span>
                                    <span class="popular-badge">Popular</span>
                                </label>

                                <label class="method-option">
                                    <input type="radio" name="payment_method" value="nagad">
                                    <span class="radio-ui"></span>
                                    <span class="method-icon nagad">N</span>
                                    <span class="method-info">
                                        <strong>Nagad</strong>
                                        <small>মোবাইল ব্যাংকিং</small>
                                    </span>
                                </label>

                                <label class="method-option">
                                    <input type="radio" name="payment_method" value="rocket">
                                    <span class="radio-ui"></span>
                                    <span class="method-icon rocket">R</span>
                                    <span class="method-info">
                                        <strong>Rocket</strong>
                                        <small>ডাচ-বাংলা ব্যাংক</small>
                                    </span>
                                </label>

                                <label class="method-option">
                                    <input type="radio" name="payment_method" value="card">
                                    <span class="radio-ui"></span>
                                    <span class="method-icon card">VISA</span>
                                    <span class="method-info">
                                        <strong>Card payment</strong>
                                        <small>Visa / Mastercard</small>
                                    </span>
                                </label>

                                

                                <div class="trust-strip">
                                    <span>🔒</span>
                                    <small>256-bit SSL encrypted · Safe & secure</small>
                                </div>
                            </div>
                        </div>

                        {{-- PLACE ORDER --}}
                        <br><button type="submit" class="btn btn-success w-100 btn-lg">
                            🛒 Place Order
                        </button>

                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<!--Payment Modal-->
<style>
    .pay-btn{
    width:100%;
    background:linear-gradient(135deg,#ff6b00,#ff9500);
    color:#fff;
    border-radius:12px;
    padding:15px;
    font-size:18px;
    font-weight:600;
    transition:.3s;
}

.pay-btn span{
    display:block;
    font-size:13px;
    opacity:.85;
}

.pay-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 20px rgba(0,0,0,.2);
}

.list-group-item{
    border-radius:10px !important;
    margin-bottom:10px;
    cursor:pointer;
    transition:.2s;
}

.list-group-item:hover{
    background:#f8f9fa;
    border-color:#0d6efd;
}
</style>
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">

            <form action="" method="POST">
                @csrf

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" style="color:white;">
                        Payment Information
                    </h5>

                    <button type="button" class="btn-close btn-close-white"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="fw-bold mb-2">
                            Select Payment Method
                        </label>

                        <div class="list-group">

                            <label class="list-group-item">
                                <input class="form-check-input me-2"
                                       type="radio"
                                       name="payment_method"
                                       value="bkash"
                                       checked>

                                <strong>bKash</strong><br>
                                <small class="text-danger">
                                    Personal: 01921588567
                                </small>
                            </label>

                            <label class="list-group-item">
                                <input class="form-check-input me-2"
                                       type="radio"
                                       name="payment_method"
                                       value="nagad">

                                <strong>Nagad</strong><br>
                                <small class="text-danger">
                                    Personal: 01921588567
                                </small>
                            </label>

                            <label class="list-group-item">
                                <input class="form-check-input me-2"
                                       type="radio"
                                       name="payment_method"
                                       value="rocket">

                                <strong>Rocket</strong><br>
                                <small class="text-danger">
                                    Personal: 019215885673
                                </small>
                            </label>

                            <label class="list-group-item">
                                <input class="form-check-input me-2"
                                       type="radio"
                                       name="payment_method"
                                       value="bank">

                                <strong>Bank Account / Credit Card</strong><br>

                                <small class="text-primary">
                                    Account Name : MH Gallery
                                </small><br>

                                <small class="text-primary">
                                    Bank : Dutch Bangla Bank PLC
                                </small><br>

                                <small class="text-primary">
                                    A/C No : 123456789012
                                </small>
                            </label>

                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            উপর থেকে আপনার পছন্দমত পেমেন্ট পদ্ধতি নির্বাচন করে পণ্যের মূল্য পরিশোধ করুন এবং Transaction ID টি প্রবেশ করুন প্রি-অর্ডার ট্রাঞ্জেকশন আইডির জায়গায়। 
                            বিস্তারিত জানতে হোয়াটসঅ্যাপে যোগাযোগ করুনঃ
                            <a href="https://wa.me/8801575020231" target="_blank">
                                +8801575020231
                            </a>
                        </label>
                    </div>

                </div>

                <div class="modal-footer">

                    <button class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            type="button">
                        Cancel
                    </button>


                </div>

            </form>

        </div>
    </div>
</div>
<!--Payment Modal end-->

<script>

    document.querySelectorAll(
    'input[name="delivery_type"]'
)
.forEach(item => {

    item.addEventListener(
        'change',
        function(){

            calculateTotal();

        });

});


document.getElementById('preorder')
.addEventListener('change', function(){

    calculateTotal();

});


function calculateTotal(){

    let delivery = parseFloat(
        document.querySelector(
            'input[name="delivery_type"]:checked'
        ).value
    );


    let subtotal = {{ $subtotal }};

    let discount = document.getElementById('preorder').checked
        ? {{ $discount }}
        : 0;


    let tax = {{ $tax }};


    let total =
        subtotal - discount + tax + delivery;


    // Delivery Update
    document.getElementById(
        'delivery_charge'
    ).value = delivery;


    document.getElementById(
        'delivery_amount'
    ).innerHTML =
    '৳' + delivery.toFixed(2);



    // Discount Update
    document.getElementById(
        'discount_input'
    ).value = discount;


    document.getElementById(
        'discountAmount'
    ).innerHTML =
    '-৳' + discount.toFixed(2);



    // Total Update
    document.getElementById(
        'grandTotal'
    ).innerHTML =
    '৳' + total.toFixed(2);


    document.getElementById(
        'final_total'
    ).value = total;

}


// Initial Load
calculateTotal();


//Preorder check section
const preorder = document.getElementById('preorder');
const preorderBox = document.getElementById('preorderBox');
const transactionId = document.getElementById('transaction_id');
const verifyBtn = document.getElementById('verifyBtn');

preorder.addEventListener('change', function () {

    if (this.checked) {
        preorderBox.style.display = 'block';
        transactionId.setAttribute('required', 'required');
    } else {
        preorderBox.style.display = 'none';
        transactionId.removeAttribute('required');
        transactionId.value = '';
    }

});

verifyBtn.addEventListener('click', function () {

    if (transactionId.value.trim() === '') {
        alert('Please enter Transaction ID');
        transactionId.focus();
        return;
    }

    // এখানে AJAX বা form submit করতে পারবেন
    alert('Transaction ID: ' + transactionId.value);

});
//preorder check end

</script>
@endsection
