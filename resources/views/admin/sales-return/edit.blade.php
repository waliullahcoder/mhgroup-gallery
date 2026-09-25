@extends('layouts.admin.edit_app')

@section('content')
    <div class="row g-3">
        <div class="col-lg-3 col-sm-6">
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
        <div class="col-lg-3 col-sm-6">
            <label for="store_id" class="form-label"><b>Store <span class="text-danger">*</span></b></label>
            <select name="store_id" id="store_id" class="select form-select" data-placeholder="Select Store" required>
                @foreach ($additionalData['stores'] as $store)
                    <option value="{{ $store->id }}"
                        {{ old('store_id', $data->store_id) == $store->id ? 'selected' : '' }}>
                        {{ $store->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-sm-6">
            <label for="return_no" class="form-label"><b>Return No. <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="return_no" name="return_no" value="{{ $data->return_no }}"
                placeholder="Return No." readonly required>
        </div>
        <div class="col-lg-3 col-sm-6">
            <label for="date" class="form-label"><b>Date <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control date_picker" id="date" name="date"
                value="{{ date('d-m-Y', strtotime(old('date', $data->date))) }}" placeholder="Date" required>
        </div>
        <div class="col-lg-3 col-sm-6">
            <label for="remarks" class="form-label"><b>Remarks</b></label>
            <textarea class="form-control" name="remarks" id="remarks" cols="30" rows="1" placeholder="Remarks">{{ old('remarks', $data->remarks) }}</textarea>
        </div>
        <div class="col-lg-3 col-sm-6">
            <label for="sales_id" class="form-label"><b>Invoice</b></label>
            <select id="sales_id" class="select form-select" data-placeholder="Select Invoice">
                <option value=""></option>
                @php
                    $sales = \App\Models\Sales::where('client_id', $data->client_id)
                        ->where(function ($query) use ($data) {
                            $query
                                ->whereColumn('net_amount', '>', 'return_amount')
                                ->orWhereIn('id', $data->list->pluck('sales_id'));
                        })
                        ->orderBy('id', 'desc')
                        ->get();
                @endphp
                @foreach ($sales as $item)
                    <option value="{{ $item->id }}">{{ $item->invoice . ' - ' . $item->formattedDate }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="sales_list_id" class="form-label"><b>Products</b></label>
            <select name="sales_list_id" id="sales_list_id" class="form-select select" data-placeholder="Select Product">
                <option value=""></option>
            </select>
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
                            <th class="px-1" width="150">Invoice</th>
                            <th class="px-1">Product</th>
                            <th width="150" class="px-1 text-end">Rate</th>
                            <th width="150" class="px-1 text-end">Quantity</th>
                            <th width="150" class="px-1 text-end">Amount</th>
                            <th class="px-1 text-center" width="40"><i class="far fa-times"></i></th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        @foreach ($data->list as $item)
                            <tr id="sales_list_{{ $item->sales_list_id }}">
                                <td class="text-center" style="padding: 0.25rem 0.25rem;">{{ $loop->iteration }}</td>
                                <td class="text-nowrap" style="padding: 2px 0.25rem;">{{ $item->sales->invoice ?? '' }}
                                </td>
                                <td class="text-nowrap" style="padding: 2px 0.25rem;">{{ $item->product->name ?? '' }} -
                                    {{ $item->edition->name ?? '' }}</td>
                                <td style="padding: 2px 0.25rem;">
                                    <input type="number" class="form-control input-sm text-end rate" step="any"
                                        id="rate_{{ $item->sales_list_id }}" name="rate[{{ $item->sales_list_id }}]"
                                        value="{{ $item->rate }}" placeholder="0.00" required>
                                </td>
                                <td style="padding: 2px 0.25rem;">
                                    <input type="number" class="form-control input-sm text-end qty" min="1"
                                        step="1" id="qty_{{ $item->sales_list_id }}"
                                        name="qty[{{ $item->sales_list_id }}]"
                                        max="{{ $item->salesList->qty - $item->salesList->return_qty + $item->qty }}"
                                        value="{{ $item->qty }}" placeholder="0.00" required>
                                </td>
                                <td style="padding: 2px 0.25rem;">
                                    <input type="number" class="form-control input-sm text-end" step="any"
                                        id="amount_{{ $item->sales_list_id }}" name="amount[{{ $item->sales_list_id }}]"
                                        value="{{ $item->amount }}" readonly placeholder="0.00" required>
                                </td>
                                <td style="padding: 2px 0.25rem;" class="text-center">
                                    <input type="hidden" class="sales_list_id" name="sales_list_id[]"
                                        value="{{ $item->sales_list_id }}">
                                    <input type="hidden" name="sales_id[{{ $item->sales_list_id }}]"
                                        value="{{ $item->sales_id }}">
                                    <input type="hidden" name="product_id[{{ $item->sales_list_id }}]"
                                        value="{{ $item->product_id }}">
                                    <input type="hidden" name="product_edition_id[{{ $item->sales_list_id }}]"
                                        value="{{ $item->product_edition_id }}">
                                    <button type="button" class="btn btn-sm btn-danger remove"><i
                                            class="far fa-times"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td style="padding: 0.25rem 0.25rem;" colspan="4"></td>
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
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('change', '#client_id', function() {
                $('#tbody tr').remove();
                $('#sales_id option').remove();
                $('#sales_list_id option').remove();
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
                            $('#sales_id').append('<option value=""></option>');
                            $.each(response.sales, function(index, item) {
                                $('#sales_id').append(
                                    `<option value="${item.id}">${item.invoice + ' - ' + item.formattedDate}</option>`
                                );
                            });
                        }
                    }
                });
            });

            $(document).on('change', '#sales_id', function() {
                var sales_id = $(this).val();
                $('#sales_list_id option').remove();

                $.ajax({
                    url: '{{ request()->fullUrl() }}',
                    type: 'POST',
                    data: {
                        _method: 'GET',
                        sales_id: sales_id
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#sales_list_id').append('<option value=""></option>');
                            $('#sales_list_id').append(response.data);
                        }
                    }
                });
            });

            $(document).on('click', '#add_item', function() {
                var sales_list_id = $('#sales_list_id').val();
                var sales_id = $('#sales_list_id option:selected').data('id');
                var invoice = $('#sales_list_id option:selected').data('invoice');
                var product = $('#sales_list_id option:selected').text();
                var product_id = $('#sales_list_id option:selected').data('product');
                var product_edition_id = $('#sales_list_id option:selected').data('edition');
                var rate = +$('#sales_list_id option:selected').data('rate');
                var qty = +$('#sales_list_id option:selected').data('qty');
                var amount = +$('#sales_list_id option:selected').data('amount');
                var sl = $('#tbody tr').length + 1;
                if (sales_list_id == null) {
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
                if ($('#sales_list_' + sales_list_id).length) {
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
                var tr =
                    `<tr id="sales_list_${sales_list_id}">
                        <td class="text-center" style="padding: 0.25rem 0.25rem;">${sl}</td>
                        <td class="text-nowrap" style="padding: 2px 0.25rem;">${invoice}</td>
                        <td class="text-nowrap" style="padding: 2px 0.25rem;">${product}</td>
                        <td style="padding: 2px 0.25rem;">
                            <input type="number" class="form-control input-sm text-end rate" step="any" id="rate_${sales_list_id}" name="rate[${sales_list_id}]" value="${rate}" placeholder="0.00" required>
                        </td>
                        <td style="padding: 2px 0.25rem;">
                            <input type="number" class="form-control input-sm text-end qty" min="1" step="1" id="qty_${sales_list_id}" name="qty[${sales_list_id}]" max="${qty}" value="${qty}" placeholder="0.00" required>
                        </td>
                        <td style="padding: 2px 0.25rem;">
                            <input type="number" class="form-control input-sm text-end" step="any" id="amount_${sales_list_id}" name="amount[${sales_list_id}]" value="${amount}" readonly placeholder="0.00" required>
                        </td>
                        <td style="padding: 2px 0.25rem;" class="text-center">
                            <input type="hidden" class="sales_list_id" name="sales_list_id[]" value="${sales_list_id}">
                            <input type="hidden" name="sales_id[${sales_list_id}]" value="${sales_id}">
                            <input type="hidden" name="product_id[${sales_list_id}]" value="${product_id}">
                            <input type="hidden" name="product_edition_id[${sales_list_id}]" value="${product_edition_id}">
                            <button type="button" class="btn btn-sm btn-danger remove"><i class="far fa-times"></i></button>
                        </td>
                    </tr>`;
                $('#tbody').append(tr);
                calculate();
            });

            $(document).on('wheel keyup change', '.rate,.qty', function() {
                calculate();
            });

            $(document).on('click', '.remove', function() {
                $(this).closest('tr').remove();
                calculate();
            });

            function calculate() {
                var total_amount = 0;
                $('.sales_list_id').each(function(index, value) {
                    var sales_list_id = $(this).val();
                    var rate = +$('#rate_' + sales_list_id).val();
                    var qty = +$('#qty_' + sales_list_id).val();
                    $('#amount_' + sales_list_id).val(rate * qty)
                    total_amount += rate * qty;
                });
                $('#total_amount').val(total_amount);
            }
        });
    </script>
@endpush
