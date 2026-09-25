@extends('layouts.admin.edit_app')

@section('content')
    <div class="row g-3">
        <div class="{{ old('sale_type', $data->sale_type) == 'Cash' ? 'col-md-2' : 'col-md-3' }} col-sm-6"
            id="sale_type_area">
            <label for="sale_type" class="form-label"><b>Sale Type <span class="text-danger">*</span></b></label>
            <select name="sale_type" id="sale_type" class="select form-select" data-placeholder="Sale Type" required>
                <option value="Credit" {{ old('sale_type', $data->sale_type) == 'Credit' ? 'selected' : '' }}>Credit
                </option>
                <option value="Cash" {{ old('sale_type', $data->sale_type) == 'Cash' ? 'selected' : '' }}>Cash</option>
            </select>
        </div>
        <div class="col-md-2 col-sm-6" id="accounts_area"
            style="display: {{ old('sale_type', $data->sale_type) == 'Cash' ? 'block' : 'none' }};">
            <label for="coa_id" class="form-label"><b>Cash Account <span class="text-danger">*</span></b></label>
            <select name="coa_id" id="coa_id" class="select form-select" data-placeholder="Select Cash Account"
                {{ old('sale_type') == 'Cash' ? 'required' : '' }}>
                <option value="">Select Cash Account</option>
                @foreach ($additionalData['cash_heads'] as $item)
                    <option value="{{ $item->id }}" {{ old('coa_id', $data->coa_id) == $item->id ? 'selected' : '' }}>
                        {{ $item->head_name . ' - ' . $item->head_code }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-sm-6">
            <label for="invoice" class="form-label"><b>Invoice No. <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="invoice" name="invoice" value="{{ $data->invoice }}" readonly
                placeholder="Invoice No." required>
        </div>
        <div class="col-md-3 col-sm-6" id="date_area">
            <label for="date" class="form-label"><b>Date <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control date_picker" id="date" name="date"
                value="{{ date('d-m-Y', strtotime(old('date', $data->date))) }}" placeholder="Date" required>
        </div>
        <div class="col-md-3 col-sm-6">
            <label for="store_id" class="form-label"><b>Store <span class="text-danger">*</span></b></label>
            <select name="store_id" id="store_id" class="select form-select" data-placeholder="Select Store" required>
                @foreach ($additionalData['stores'] as $item)
                    <option value="{{ $item->id }}"
                        {{ old('store_id', $data->store_id) == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-sm-6">
            <label for="client_id" class="form-label"><b>Client <span class="text-danger">*</span></b></label>
            <select name="client_id" id="client_id" class="select form-select" data-placeholder="Select Client" required>
                <option value=""></option>
                @foreach ($additionalData['clients'] as $item)
                    <option value="{{ $item->id }}"
                        {{ old('client_id', $data->client_id) == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-sm-6">
            <label for="product_id" class="form-label"><b>Books</b></label>
            <select id="product_id" class="select form-select" data-placeholder="Select Product">
                <option value=""></option>
                @foreach ($additionalData['products'] as $item)
                    <option value="{{ $item->id }}" data-price="{{ $item->client_price }}"
                        data-commission="{{ $item->client_commission }}" data-rate="{{ $item->net_price }}">
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-sm-6">
            <label for="product_edition_id" class="form-label"><b>Editions</b></label>
            <select id="product_edition_id" class="select form-select" data-placeholder="Select Book Editions">
                <option value=""></option>
            </select>
        </div>
        <div class="col-md-3 col-sm-6">
            <label for="sales_officer_id" class="form-label"><b>TSO <span class="text-danger">*</span></b></label>
            <select id="sales_officer_id" name="sales_officer_id" class="select form-select" data-placeholder="Select TSO" required>
                <option value=""></option>
                @foreach ($additionalData['salesOfficers'] as $item)
                    <option value="{{ $item->id }}" {{ old('sales_officer_id', $data->sales_officer_id) == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="remarks" class="form-label"><b>Remarks</b></label>
            <input type="text" class="form-control" name="remarks" id="remarks" placeholder="Remarks">
        </div>
        <div class="col-md-2 col-sm-6">
            <label for="credit_limit" class="form-label"><b>Credit Limit</b></label>
            <input type="number" class="form-control" id="credit_limit" name="credit_limit" placeholder="Credit Limit"
                value="{{ $additionalData['credit_limit'] }}" readonly>
            <input type="hidden" id="limitation" value="{{ $additionalData['limitation'] }}">
        </div>
        <div class="col-md-2 col-6">
            <label for="stock" class="form-label"><b>Stock</b></label>
            <input type="number" class="form-control" id="stock" step="any" value="0"
                placeholder="Stock" readonly>
        </div>
        <div class="col-md-2 col-6">
            <label for="quantity" class="form-label"><b>Quantity</b></label>
            <input type="number" class="form-control" id="quantity" step="any" value="1"
                placeholder="Quantity">
        </div>
        <div class="col-md-2 col-sm-6">
            <label class="form-label text-white d-sm-block d-none"><b>Add Item</b></label>
            <button type="button" class="btn btn-xs btn-primary w-100 py-2" id="add_item">Add Product</button>
        </div>
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="bg-primary border-primary text-white text-nowrap">
                        <tr>
                            <th class="px-2 text-center" width="30">SL#</th>
                            <th class="px-1">Book</th>
                            <th class="px-1">Edition</th>
                            <th class="px-1 text-end" width="120">Price</th>
                            <th class="px-1 text-end" width="120">Commission %</th>
                            <th class="px-1 text-end" width="120">Net Price</th>
                            <th class="px-1 text-end" width="120">Quantity</th>
                            <th class="px-1 text-end" width="120">Amount</th>
                            <th class="px-1 text-center" width="40"><i class="far fa-times"></i></th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        @foreach ($data->list as $item)
                            <tr id="edition_{{ $item->product_edition_id }}">
                                <td class="text-center" style="padding: 0.25rem 0.25rem;">{{ $loop->iteration }}</td>
                                <td class="text-nowrap" style="padding: 2px 0.25rem;">{{ $item->product->name }}</td>
                                <td class="text-nowrap" style="padding: 2px 0.25rem;">{{ $item->edition->name }}</td>
                                <td style="padding: 2px 0.25rem;">
                                    <input type="number" class="form-control input-sm text-end price" step="any"
                                        id="price_{{ $item->product_edition_id }}"
                                        data-id="{{ $item->product_edition_id }}"
                                        name="price[{{ $item->product_edition_id }}]" value="{{ $item->price }}"
                                        placeholder="0.00" required>
                                </td>
                                <td style="padding: 2px 0.25rem;">
                                    <input type="number" class="form-control input-sm text-end commission"
                                        step="any" id="commission_{{ $item->product_edition_id }}"
                                        data-id="{{ $item->product_edition_id }}"
                                        name="commission[{{ $item->product_edition_id }}]"
                                        value="{{ $item->commission }}" placeholder="0.00" required>
                                </td>
                                <td style="padding: 2px 0.25rem;">
                                    <input type="number" class="form-control input-sm text-end rate" step="any"
                                        id="rate_{{ $item->product_edition_id }}"
                                        name="rate[{{ $item->product_edition_id }}]" value="{{ $item->rate }}"
                                        placeholder="0.00" readonly required>
                                </td>
                                @php
                                    $stock =
                                        \App\HelperClass::stock($item->product_edition_id, $item->store_id) +
                                        $item->qty;
                                @endphp
                                <td style="padding: 2px 0.25rem;">
                                    <input type="number" class="form-control input-sm text-end qty" min="1"
                                        step="1" id="qty_{{ $item->product_edition_id }}"
                                        name="qty[{{ $item->product_edition_id }}]" max="{{ $stock }}"
                                        value="{{ $item->qty }}" placeholder="0.00" required>
                                </td>
                                <td style="padding: 2px 0.25rem;">
                                    <input type="number" class="form-control input-sm text-end" step="any"
                                        id="amount_{{ $item->product_edition_id }}"
                                        name="amount[{{ $item->product_edition_id }}]" value="{{ $item->amount }}"
                                        readonly placeholder="0.00" required>
                                </td>
                                <td style="padding: 2px 0.25rem;" class="text-center">
                                    <input type="hidden" class="product_edition_id" name="product_edition_id[]"
                                        value="{{ $item->product_edition_id }}">
                                    <input type="hidden" name="product_id[{{ $item->product_edition_id }}]"
                                        value="{{ $item->product_id }}">
                                    <button type="button" class="btn btn-sm btn-danger remove"><i
                                            class="far fa-times"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td style="padding: 0.25rem 0.25rem;" colspan="6"></td>
                            <td style="padding: 2px 0.25rem;" colspan="3">
                                <div class="input-group align-items-center">
                                    <b style="width: 100px;">Total</b>
                                    <input type="number" id="total_amount" name="total_amount" readonly
                                        class="form-control input-sm text-end" placeholder="Total Amount"
                                        value="{{ $data->amount }}">
                                    <span class="text-center" style="width: 40px;">TK.</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0.25rem 0.25rem;" colspan="6"></td>
                            <td style="padding: 2px 0.25rem;" colspan="3">
                                <div class="input-group align-items-center">
                                    <b style="width: 100px;">Discount</b>
                                    <input type="number" id="discount" name="discount"
                                        class="form-control input-sm text-end" placeholder="Discount"
                                        value="{{ $data->discount }}">
                                    <span class="text-center" style="width: 40px;">TK.</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0.25rem 0.25rem;" colspan="6"></td>
                            <td style="padding: 2px 0.25rem;" colspan="3">
                                <div class="input-group align-items-center">
                                    <b style="width: 100px;">Net Amount</b>
                                    <input type="number" id="net_amount" name="net_amount" readonly
                                        class="form-control input-sm text-end" placeholder="net Amount"
                                        value="{{ $data->net_amount }}">
                                    <span class="text-center" style="width: 40px;">TK.</span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="text-end text-danger mt-3" id="limit_crosed" style="display: none;">This bill has
            exceeded its due limit</div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('change', '#sale_type', function() {
                var sale_type = $(this).val();
                if (sale_type == 'Cash') {
                    $('#accounts_area').show();
                    $('#coa_id').prop('required', true);
                    $('#sale_type_area').removeClass('col-md-3').addClass('col-md-2');
                } else {
                    $('#accounts_area').hide();
                    $('#coa_id').prop('required', false);
                    $('#sale_type_area').removeClass('col-md-2').addClass('col-md-3');
                }
                dueLimit();
            });

            $(document).on('change', '#client_id', function() {
                var client_id = $(this).val();
                $.ajax({
                    url: '{{ request()->fullUrl() }}',
                    type: 'POST',
                    data: {
                        _method: 'GET',
                        client_id: client_id
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#credit_limit').val(response.credit_limit);
                            $('#limitation').val(response.limitation);
                            dueLimit();
                        }
                    }
                });
            });

            function dueLimit() {
                var credit_limit = +$('#credit_limit').val();
                var net_amount = +$('#net_amount').val();
                var limitation = $('#limitation').val();
                var sale_type = $('#sale_type').val();
                if (sale_type == 'Credit' && limitation != '' && credit_limit < net_amount) {
                    $('#limit_crosed').show();
                    $('.submit_btn').prop('disabled', true);
                } else {
                    $('#limit_crosed').hide();
                    $('.submit_btn').prop('disabled', false);
                }
            }

            $(document).on('change', '#store_id', function() {
                $('#tbody tr').remove();
                checkStock();
            });

            $(document).on('change', '#product_id', function() {
                var product_id = $(this).val();
                $('#product_edition_id option').remove();
                $('#product_edition_id').append(`<option value=""></option>`);
                if (product_id) {
                    $.ajax({
                        url: '{{ request()->fullUrl() }}',
                        type: 'POST',
                        data: {
                            _method: 'GET',
                            product_id: product_id,
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                $.each(response.editions, function(key, value) {
                                    $('#product_edition_id').append(
                                        `<option value="${value.id}">${value.name}</option>`
                                    );
                                });
                            }
                        }
                    });
                }
            });

            $(document).on('change', '#product_edition_id', function() {
                checkStock();
            });

            function checkStock() {
                $('#stock').val(0);
                var store_id = $('#store_id').val();
                var product_edition_id = $('#product_edition_id').val();
                if (store_id) {
                    $.ajax({
                        url: '{{ request()->fullUrl() }}',
                        type: 'POST',
                        data: {
                            _method: 'GET',
                            store_id: store_id,
                            product_edition_id: product_edition_id
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                $('#stock').val(response.stock);
                            }
                        }
                    });
                }
            }

            $(document).on('click', '#add_item', function() {
                var product_id = $('#product_id option:selected').val();
                var product_edition_id = $('#product_edition_id').val();
                var edition = $('#product_edition_id option:selected').text();
                var product = $('#product_id option:selected').text();

                var price = +$('#product_id option:selected').data('price');
                var commission = +$('#product_id option:selected').data('commission');
                var rate = +$('#product_id option:selected').data('rate');

                var qty = +$('#quantity').val();
                var stock = +$('#stock').val();
                var sl = $('#tbody tr').length + 1;
                if (product_edition_id == '') {
                    Swal.fire({
                        width: "22rem",
                        toast: true,
                        position: 'top-right',
                        text: "Please select a product!",
                        icon: "error",
                        showConfirmButton: false,
                        timer: 1500,
                        showClass: {
                            popup: `animate__animated animate__bounceInRight animate__faster`
                        },
                        hideClass: {
                            popup: `animate__animated animate__bounceOutRight animate__faster`
                        }
                    });
                    return false;
                }
                if ($('#edition_' + product_edition_id).length) {
                    Swal.fire({
                        width: "22rem",
                        toast: true,
                        position: 'top-right',
                        text: "Product already added!",
                        icon: "error",
                        showConfirmButton: false,
                        timer: 1500,
                        showClass: {
                            popup: `animate__animated animate__bounceInRight animate__faster`
                        },
                        hideClass: {
                            popup: `animate__animated animate__bounceOutRight animate__faster`
                        }
                    });
                    return false;
                }
                if (stock <= 0 || qty > stock) {
                    Swal.fire({
                        width: "22rem",
                        toast: true,
                        position: 'top-right',
                        text: "Stock not available!",
                        icon: "error",
                        showConfirmButton: false,
                        timer: 1500,
                        showClass: {
                            popup: `animate__animated animate__bounceInRight animate__faster`
                        },
                        hideClass: {
                            popup: `animate__animated animate__bounceOutRight animate__faster`
                        }
                    });
                    return false;
                }
                var tr =
                    `<tr id="edition_${product_edition_id}">
                        <td class="text-center" style="padding: 0.25rem 0.25rem;">${sl}</td>
                        <td class="text-nowrap" style="padding: 2px 0.25rem;">${product}</td>
                        <td class="text-nowrap" style="padding: 2px 0.25rem;">${edition}</td>
                        <td style="padding: 2px 0.25rem;">
                            <input type="number" class="form-control input-sm text-end price" step="any" id="price_${product_edition_id}" data-id="${product_edition_id}" name="price[${product_edition_id}]" value="${price}" placeholder="0.00" required>
                        </td>
                        <td style="padding: 2px 0.25rem;">
                            <input type="number" class="form-control input-sm text-end commission" step="any" id="commission_${product_edition_id}" data-id="${product_edition_id}" name="commission[${product_edition_id}]" value="${commission}" placeholder="0.00" required>
                        </td>
                        <td style="padding: 2px 0.25rem;">
                            <input type="number" class="form-control input-sm text-end rate" step="any" id="rate_${product_edition_id}" name="rate[${product_edition_id}]" value="${rate}" placeholder="0.00" readonly required>
                        </td>
                        <td style="padding: 2px 0.25rem;">
                            <input type="number" class="form-control input-sm text-end qty" min="1" step="1" id="qty_${product_edition_id}" name="qty[${product_edition_id}]" max="${stock}" value="${qty}" placeholder="0.00" required>
                        </td>
                        <td style="padding: 2px 0.25rem;">
                            <input type="number" class="form-control input-sm text-end" step="any" id="amount_${product_edition_id}" name="amount[${product_edition_id}]" value="${rate*qty}" readonly placeholder="0.00" required>
                        </td>
                        <td style="padding: 2px 0.25rem;" class="text-center">
                            <input type="hidden" class="product_edition_id" name="product_edition_id[]" value="${product_edition_id}">
                            <input type="hidden" name="product_id[${product_edition_id}]" value="${product_id}">
                            <button type="button" class="btn btn-sm btn-danger remove"><i class="far fa-times"></i></button>
                        </td>
                    </tr>`;
                $('#tbody').append(tr);
                calculate();
                dueLimit();
            });

            $(document).on('wheel keyup change', '.qty,#discount', function() {
                calculate();
                dueLimit();
            });

            $(document).on('click', '.remove', function() {
                $(this).closest('tr').remove();
                calculate();
                dueLimit();
            });

            $(document).on('wheel keyup change', '.price,.commission', function() {
                var id = $(this).data('id');
                var price = +$('#price_' + id).val();
                var commission = +$('#commission_' + id).val();
                if (commission > 100) {
                    commission = 100;
                    $('#commission_' + id).val(100);
                }
                var rate = Math.round(price - (price * (commission / 100)));
                $('#rate_' + id).val(rate);
                calculate();
                dueLimit();
            });

            function calculate() {
                var total_amount = 0;
                $('.product_edition_id').each(function(index, value) {
                    var product_edition_id = $(this).val();
                    var rate = +$('#rate_' + product_edition_id).val();
                    var qty = +$('#qty_' + product_edition_id).val();
                    $('#amount_' + product_edition_id).val(rate * qty)
                    total_amount += rate * qty;
                });
                $('#total_amount').val(total_amount);
                var discount = +$('#discount').val();
                $('#net_amount').val(total_amount - discount);
            }
        });
    </script>
@endpush
