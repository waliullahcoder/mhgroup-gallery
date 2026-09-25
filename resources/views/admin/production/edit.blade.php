@extends('layouts.admin.edit_app')

@section('content')
    <div class="row g-3">
        <div class="col-md-4 col-sm-6">
            <label for="production" class="form-label"><b>Production No. <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="production" name="production" value="{{ $data->production_no }}"
                readonly placeholder="Production No." required>
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="date" class="form-label"><b>Date <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control date_picker" id="date" name="date"
                value="{{ date('d-m-Y', strtotime(old('date', $data->date))) }}" placeholder="Date" required>
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="store_id" class="form-label"><b>Store</b></label>
            <select id="store_id" name="store_id" class="select form-select" data-placeholder="Select Store">
                @foreach ($additionalData['stores'] as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="product_id" class="form-label"><b>Books</b></label>
            <select id="product_id" class="select form-select" data-placeholder="Select Book">
                <option value=""></option>
                @foreach ($additionalData['products'] as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="product_edition_id" class="form-label"><b>Editions</b></label>
            <select id="product_edition_id" class="select form-select" data-placeholder="Select Book Editions">
                <option value=""></option>
            </select>
        </div>
        <div class="col-md-2 col-6">
            <label for="quantity" class="form-label"><b>Quantity</b></label>
            <input type="number" class="form-control" id="quantity" step="any" value="1" placeholder="Quantity">
        </div>
        <div class="col-md-2 col-6">
            <label class="form-label text-white"><b>Add Item</b></label>
            <button type="button" class="btn btn-xs btn-primary w-100 py-2" id="add_item">Add Product</button>
        </div>
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="bg-primary border-primary text-white text-nowrap">
                        <tr>
                            <th class="px-2 text-center" width="30">SL#</th>
                            <th class="px-1">Book Name</th>
                            <th class="px-1">Edition</th>
                            <th class="px-1 text-end" width="250">Quantity</th>
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
                                    <input type="number" class="form-control input-sm text-end qty" min="1"
                                        step="1" id="qty_{{ $item->product_edition_id }}"
                                        name="qty[{{ $item->product_edition_id }}]" value="{{ $item->qty }}"
                                        placeholder="0.00" required>
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
                            <td class="text-end" style="padding: 0.25rem 0.25rem;" colspan="3">
                                <b style="width: 100px;">Total Qty</b>
                            </td>
                            <td style="padding: 2px 0.25rem;" colspan="1">
                                <input type="number" id="totalQty" name="total_qty" readonly
                                    class="form-control input-sm text-end" placeholder="Total Qty"
                                    value="{{ $data->total_qty }}">
                            </td>
                            <td style="padding: 2px 0.25rem;"></td>
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
            $(document).on('click', '#add_item', function() {
                var product_id = $('#product_id option:selected').val();
                var product = $('#product_id option:selected').text();
                var product_edition_id = $('#product_edition_id option:selected').val();
                var edition = $('#product_edition_id option:selected').text();
                var qty = +$('#quantity').val();
                var sl = $('#tbody tr').length + 1;
                if (product_id == '') {
                    Swal.fire({
                        width: "22rem",
                        toast: true,
                        position: 'top-right',
                        text: "Please select a book!",
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
                if (product_edition_id == '') {
                    Swal.fire({
                        width: "22rem",
                        toast: true,
                        position: 'top-right',
                        text: "Please select a book edition!",
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
                        text: "Book Edition already added!",
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
                            <input type="number" class="form-control input-sm text-end qty" min="1" step="1" id="qty_${product_edition_id}" name="qty[${product_edition_id}]" value="${qty}" placeholder="0.00" required>
                        </td>
                        <td style="padding: 2px 0.25rem;" class="text-center">
                            <input type="hidden" class="product_edition_id" name="product_edition_id[]" value="${product_edition_id}">
                            <input type="hidden" name="product_id[${product_edition_id}]" value="${product_id}">
                            <button type="button" class="btn btn-sm btn-danger remove"><i class="far fa-times"></i></button>
                        </td>
                    </tr>`;
                $('#tbody').append(tr);
                calculate();
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

            $(document).on('wheel keyup change', '.qty', function() {
                calculate();
            });

            $(document).on('click', '.remove', function() {
                $(this).closest('tr').remove();
                calculate();
            });

            function calculate() {
                var totalQty = 0;
                $('.product_edition_id').each(function(index, value) {
                    var product_edition_id = $(this).val();
                    totalQty += +$('#qty_' + product_edition_id).val();
                });
                $('#totalQty').val(totalQty);
            }
        });
    </script>
@endpush
