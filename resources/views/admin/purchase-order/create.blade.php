@extends('layouts.admin.app')

@section('content')
<form action="{{ route('admin.purchase-order.store') }}" method="POST" id="purchaseOrderForm">
@csrf
<div class="row g-3">
    <div class="col-lg-8">
        

        {{-- PRODUCT GRID / SEARCH --}}
       <div class="card border-dashed mb-2">
    <div class="card-body">
        <div class="d-flex gap-2 align-items-center">
            {{-- Link to create new product --}}
            <a class="btn btn-sm btn-secondary px-3" href="{{ route('admin.product.create') }}" title="Add New Product">
                <i class="fas fa-plus"></i>
            </a>

            {{-- Searchable select input like input box --}}
            <select id="product_select" class="form-select" data-placeholder="Enter Book name" style="flex: 1;">
                <option value=""></option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}">
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>

            {{-- Add product button --}}
            <button class="btn btn-sm btn-secondary" id="addProductBtn" type="button">
               +Add
            </button>
        </div>
    </div>
</div>

        {{-- PURCHASE ITEMS TABLE --}}
        <div class="card border-dashed paper-cut">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5>Purchase Items</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="po-items-table">
                        <thead>
                            <tr class="register-items-header text-nowrap">
                                <th>Item Name</th>
                                <th width="15%">Cost</th>
                                <th width="12%">Qty</th>
                                <th width="15%">Total</th>
                                <th width="8%">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="row g-2">
            {{-- Supplier / Store / Date --}}
            <div class="col-12">
                <div class="card border-dashed">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="vendor_id" class="form-label"><b>Supplier <span class="text-danger">*</span></b></label>
                                <select name="vendor_id" id="vendor_id" class="form-select select" data-placeholder="Select Supplier" required>
                                    <option value=""></option>
                                    @foreach ($vendors as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="store_id" class="form-label"><b>Store</b></label>
                                <select name="store_id" id="store_id" class="form-select select" data-placeholder="Select Store">
                                    <option value=""></option>
                                    @foreach ($stores as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="order_date" class="form-label"><b>Order Date</b></label>
                                <input type="text" name="order_date" class="form-control date_picker" value="{{ date('d-m-Y') }}" required>
                            </div>

                            <div class="col-12">
                                <label for="expected_date" class="form-label"><b>Expected Date</b></label>
                                <input type="text" name="expected_date" class="form-control date_picker" value="{{ date('d-m-Y') }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DISCOUNT & TOTALS --}}
            <div class="col-12">
                <div class="card border-dashed paper-cut">
                    <div class="card-body p-0">
                        <ul class="list-group rounded-0" style="margin: -1px;">
                            <li class="list-group-item border-dashed">
                                <span class="value float-end">
                                    <input type="number" name="discount_percent" id="discount_percent" class="form-control form-control-sm" value="0" min="0" max="100">
                                </span>
                            </li>
                            <li class="sub-total list-group-item border-dashed">
                                <span class="key">Sub Total:</span>
                                <span class="value float-end" id="subtotal">৳0.00</span>
                            </li>
                            <li class="list-group-item border-dashed">
                                <span class="key">Tax ({{$settings->tax}}%):</span>
                                <span class="value float-end" id="tax_total">৳0.00</span>
                            </li>
                             <li class="discount list-group-item border-dashed">
                                <span class="key">Discount(%)</span>
                                <span class="value float-end">
                                    <span id="discount_total">৳0.00</span>
                                </span>
                            </li>
                             
                            <li class="list-group-item border-dashed">
                                <span class="key">Grand Total:</span>
                                <span class="value float-end" id="grand_total_display">৳0.00</span>
                            </li>
                        </ul>

                        <input type="hidden" name="total_amount" id="total_amount" value="0">
                        <input type="hidden" name="tax_amount" id="tax_amount" value="0">
                        <input type="hidden" name="discount_amount" id="discount_amount" value="0">
                        <input type="hidden" name="grand_total" id="grand_total" value="0">

                        <div class="add-payment mt-3">
                            <div class="side-heading fs-12">Add Payment</div>
                            <div class="row g-2">
                                <div class="col-auto">
                                    <label class="radio-input">Cash
                                        <input type="radio" name="payment_type" value="Cash" checked>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="radio-input">Check
                                        <input type="radio" name="payment_type" value="Check">
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="radio-input">Gift Card
                                        <input type="radio" name="payment_type" value="Gift Card">
                                    </label>
                                </div>
                            </div>
                            <div class="input-group add-payment-form mt-2">
                                <input type="number" name="paid_amount" value="0.00" id="amount_tendered" step="any" class="add-input numKeyboard form-control" placeholder="Enter Cash Amount">
                            </div>
                        </div>

                        <div class="comment-block mt-3">
                            <label for="notes" class="side-heading fs-12">Comments :</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="finish-sale mt-3">
                            <button type="submit" class="btn btn-light-success w-100">Finish</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</form>
@endsection

@push('js')

<script>
$(document).ready(function(){
    let rowIdx = 0;
    let selectedProducts = {};

    // INIT select2
    $('#product_select').select2({width:'100%'});

    // ADD PRODUCT
    $('#addProductBtn').click(function(){
        let productSelect = $('#product_select');
        let productId = productSelect.val();
        if(!productId) return;

        if(selectedProducts[productId]){
            // Increment quantity only
            let qtyInput = $('#po-items-table tbody tr[data-id="'+productId+'"]').find('.qty');
            qtyInput.val(parseInt(qtyInput.val())+1).trigger('input');
            return;
        }

        let productName = productSelect.find('option:selected').text();
        let price = parseFloat(productSelect.find('option:selected').data('price')) || 0;

        let row = `<tr data-id="${productId}">
            <td>${productName}<input type="hidden" name="items[${rowIdx}][product_id]" value="${productId}"></td>
            <td><input type="number" name="items[${rowIdx}][unit_price]" value="${price}" class="form-control price" readonly></td>
            <td><input type="number" name="items[${rowIdx}][quantity]" value="1" min="1" class="form-control qty"></td>
            <td><input type="text" class="form-control line-total" readonly></td>
            <td><button type="button" class="btn btn-sm btn-danger removeRow">X</button></td>
        </tr>`;

        $('#po-items-table tbody').append(row);
        selectedProducts[productId] = true;
        rowIdx++;
        calculateTotals();
    });

    // REMOVE ROW
    $(document).on('click', '.removeRow', function(){
        let row = $(this).closest('tr');
        let productId = row.data('id');
        delete selectedProducts[productId];
        row.remove();
        calculateTotals();
    });

    // QUANTITY CHANGE
    $(document).on('input', '.qty', function(){
        calculateTotals();
    });

    // DISCOUNT CHANGE
    $(document).on('input', '#discount_percent', function(){
         var val = +$(this).val();
            if(val > 100) {
                $(this).val(100);
                Swal.fire({
                    toast: true,
                    position: 'top-right',
                    icon: 'error',
                    title: 'Maximum discount is 100%',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        calculateTotals();
    });

    function calculateTotals(){
        let subtotal = 0;
        $('#po-items-table tbody tr').each(function(){
            let qty = parseFloat($(this).find('.qty').val()) || 0;
            let price = parseFloat($(this).find('.price').val()) || 0;
            let lineTotal = qty * price;
            $(this).find('.line-total').val(lineTotal.toFixed(2));
            subtotal += lineTotal;
        });
        const TAX_RATE = {{ $settings->tax/100 }};
        let discountPercent = parseFloat($('#discount_percent').val()) || 0;
        let discountAmount = subtotal * (discountPercent / 100);
        let tax = subtotal * TAX_RATE;
        let grandTotal = subtotal - discountAmount + tax;

        // UPDATE DISPLAY
        $('#subtotal').text('৳'+subtotal.toFixed(2));
        $('#tax_total').text('৳'+tax.toFixed(2));
        $('#grand_total_display').text('৳'+grandTotal.toFixed(2));
         $("#discount_total").text("৳" + discountAmount.toFixed(2));
        // UPDATE HIDDEN INPUTS FOR FORM
        $('#total_amount').val(subtotal.toFixed(2));
        $('#discount_amount').val(discountAmount.toFixed(2));
        $('#tax_amount').val(tax.toFixed(2));
        $('#grand_total').val(grandTotal.toFixed(2));
    }

});
</script>
@endpush