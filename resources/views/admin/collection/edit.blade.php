@extends('layouts.admin.edit_app')

@section('content')
    <div class="row g-3">
        <div class="col-md-4 col-sm-6">
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
        <div class="col-md-4 col-sm-6">
            <label for="date" class="form-label"><b>Date <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control date_picker" id="date" name="date"
                value="{{ date('d-m-Y', strtotime(old('date', $data->date))) }}" placeholder="Date" required>
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="payment_no" class="form-label"><b>Payment No. <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="payment_no" name="payment_no" value="{{ $data->payment_no }}"
                placeholder="Payment No." readonly required>
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="payment_type" class="form-label"><b>Payment Type <span class="text-danger">*</span></b></label>
            <select name="payment_type" id="payment_type" class="select form-select" data-placeholder="Payment Type"
                required>
                <option value="Cash" {{ old('payment_type', $data->payment_type) == 'Cash' ? 'selected' : '' }}>Cash
                </option>
                <option value="Bank" {{ old('payment_type', $data->payment_type) == 'Bank' ? 'selected' : '' }}>Bank
                </option>
            </select>
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="coa_id" class="form-label"><b>Account Head <span
                        class="text-danger custom_required">{{ old('collection_type', $data->collection_type) != 'Adjust' ? '*' : '' }}</span></b></label>
            <select name="coa_id" id="coa_id" class="select form-select" data-placeholder="Select Account Head"
                {{ old('collection_type', $data->collection_type) != 'Adjust' ? 'required' : '' }}>
                <option value=""></option>
                @foreach ($additionalData['cash_heads'] as $item)
                    <option value="{{ $item->id }}" {{ old('coa_id', $data->coa_id) == $item->id ? 'selected' : '' }}>
                        {{ $item->head_name . ' - ' . $item->head_code }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="collection_type" class="form-label"><b>Collection Type <span
                        class="text-danger">*</span></b></label>
            <select name="collection_type" id="collection_type" class="select form-select" required>
                <option value="Payment"
                    {{ old('collection_type', $data->collection_type) == 'Payment' ? 'selected' : '' }}>Invoice</option>
                <option value="Advance"
                    {{ old('collection_type', $data->collection_type) == 'Advance' ? 'selected' : '' }}>Advance</option>
                <option value="Adjust" {{ old('collection_type', $data->collection_type) == 'Adjust' ? 'selected' : '' }}>
                    Adjust</option>
            </select>
        </div>
        <div class="col-sm-6">
            <label for="remarks" class="form-label"><b>Remarks</b></label>
            <textarea class="form-control" name="remarks" id="remarks" cols="30" rows="1" placeholder="Remarks">{{ old('remarks', $data->remarks) }}</textarea>
        </div>
        <div class="col-md-2 col-6">
            <label for="due" class="form-label"><b>Due</b></label>
            <input type="text" class="form-control" id="due" name="due" placeholder="Due"
                value="{{ $additionalData['due'] }}" readonly>
        </div>
        <div class="col-md-2 col-6">
            <label for="advance" class="form-label"><b>Advance</b></label>
            <input type="text" class="form-control" id="advance" name="advance" placeholder="Advance"
                value="{{ $additionalData['advance'] }}" readonly>
        </div>
        <div class="col-md-2 col-sm-6">
            <label for="total_amount" class="form-label text-nowrap"><b>Total Collection</b></label>
            <input type="text" class="form-control" id="total_amount" name="total_amount" value="{{ $data->amount }}"
                placeholder="Total Collection">
        </div>
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="bg-primary border-primary text-white text-nowrap">
                        <tr>
                            <th class="text-center px-2" width="30">SL#</th>
                            <th class="px-1">Invoice</th>
                            <th class="px-1 text-end">Sales Amount</th>
                            <th class="px-1 text-end">Previous Collection</th>
                            <th class="px-1 text-end">Current Collection</th>
                            <th class="px-1 text-end">Due Amount</th>
                            <th class="px-1 text-center" width="40">
                                <div class="custom-control custom-checkbox mx-auto"
                                    style="min-height: 14px;max-height: 14px">
                                    <input type="checkbox" class="custom-control-input" id="checkAll">
                                    <label for="checkAll" class="custom-control-label"></label>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        @if ($data->type != 'Advance')
                            @include('admin.collection.partial.edit-rows', [
                                'sales' => $additionalData['sales'],
                                'advance' => $additionalData['advance'],
                                'data' => $data,
                                'type' => $data->type,
                            ])
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            if ($('.sales_id').length > 0 && $('.sales_id').length == $('.sales_id:checked').length) {
                $('#checkAll').prop('checked', true);
            } else {
                $('#checkAll').prop('checked', false);
            }

            $(document).on('change', '#payment_type', function() {
                var payment_type = $(this).val();
                $('#coa_id option').remove();
                $.ajax({
                    url: '{{ request()->fullUrl() }}',
                    type: 'POST',
                    data: {
                        _method: 'GET',
                        payment_type: payment_type,
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#coa_id').append(`<option value=""></option>`);
                            $.each(response.cash_heads, function(key, value) {
                                $('#coa_id').append(
                                    `<option value="${value.id}">${value.head_name}-${value.head_code}</option>`
                                );
                            });
                        }
                    }
                });
            });

            $(document).on('change', '#client_id', function() {
                $('#due').val(0);
                $('#advance').val(0);
                $('#tbody tr').remove();
                let collection_type = $('#collection_type').val();
                if (collection_type == 'Payment' || collection_type == 'Adjust') {
                    clientSales();
                    validation();
                    distribute();
                }
            });

            $(document).on('change', '#collection_type', function(e) {
                let collection_type = $(this).val();
                $('#coa_id').prop('required', true);
                $('.custom_required').text('*');
                if (collection_type == 'Adjust') {
                    $('.custom_required').text('');
                    $('#coa_id').prop('required', false);
                }
                if (collection_type == 'Payment' || collection_type == 'Adjust') {
                    clientSales();
                    validation();
                    distribute();
                } else {
                    $('#tbody tr').remove();
                }
            });

            function clientSales() {
                var client_id = $('#client_id').val();
                var collection_type = $('#collection_type').val();
                $('#tbody tr').remove();
                $.ajax({
                    url: '{{ request()->fullUrl() }}',
                    type: 'POST',
                    data: {
                        _method: 'GET',
                        client_id: client_id,
                        collection_type: collection_type,
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#tbody').html(response.data);
                            $('#due').val(response.due);
                            $('#advance').val(response.advance);
                            validation();
                            calculate();
                        }
                    }
                });
            }

            $(document).on('click', '#checkAll', function(e) {
                if ($(this).prop('checked')) {
                    var due = +$('#due').val();
                    var advance = +$('#advance').val();
                    var collection_type = $('#collection_type').val();
                    if (collection_type == 'Adjust') {
                        $('.sales_id').each(function(key, value) {
                            if (advance <= 0) {
                                $(this).prop('checked', false);
                                return true;
                            }
                            var sales_id = $(this).val();
                            var payable = $(this).data('payable');
                            if (advance > payable) {
                                $('#amount_' + sales_id).val(payable);
                                $('#due_' + sales_id).val(0);
                                advance -= payable;
                            } else {
                                $('#amount_' + sales_id).val(advance);
                                $('#due_' + sales_id).val(payable - advance);
                                advance = 0;
                            }
                            $(this).prop('checked', true);
                        });
                    } else {
                        $('.sales_id').each(function(key, value) {
                            var sales_id = $(this).val();
                            var payable = $(this).data('payable');
                            $('#amount_' + sales_id).val(payable);
                            $('#due_' + sales_id).val(0);
                            $(this).prop('checked', true);
                        });
                    }
                } else {
                    $('.sales_id').prop('checked', false);
                    $('.sales_id').each(function(key, value) {
                        var sales_id = $(this).val();
                        var payable = $(this).data('payable');
                        $('#amount_' + sales_id).val(0);
                        $('#due_' + sales_id).val(payable);
                    });
                }
                calculate();
            });

            $(document).on('wheel keyup change keypress', '#total_amount', function(e) {
                var keycode = (e.keyCode ? e.keyCode : e.which);
                if (keycode == '13') {
                    e.preventDefault();
                }
                validation();
                distribute();
            });

            function validation() {
                var total_amount = +$('#total_amount').val();
                var due = +$('#due').val();
                var advance = +$('#advance').val();
                var collection_type = $('#collection_type').val();
                if (collection_type == 'Payment' && total_amount > due) {
                    $('#total_amount').val(due);
                }
                if (collection_type == 'Adjust' && total_amount > advance) {
                    $('#total_amount').val(advance);
                }
            }

            function distribute() {
                var total_amount = +$('#total_amount').val();
                $('.sales_id').each(function() {
                    var sales_id = $(this).val();
                    var payable = $(this).data('payable');
                    if (total_amount > 0) {
                        if (payable > total_amount) {
                            $('#amount_' + sales_id).val(total_amount);
                            $('#due_' + sales_id).val(payable - total_amount);
                            total_amount = 0;
                        } else {
                            $('#amount_' + sales_id).val(payable);
                            $('#due_' + sales_id).val(0);
                            total_amount -= payable;
                        }
                        $(this).prop('checked', true);
                    } else {
                        $('#amount_' + sales_id).val(0);
                        $('#due_' + sales_id).val(payable);
                        $(this).prop('checked', false);
                    }
                });

                if ($('.sales_id').length > 0 && $('.sales_id').length == $('.sales_id:checked').length) {
                    $('#checkAll').prop('checked', true);
                } else {
                    $('#checkAll').prop('checked', false);
                }
            }

            $(document).on('wheel keyup change', '.amount', function(e) {
                var amount = +$(this).val();
                var max = +$(this).attr('max');
                if (amount > max) {
                    $(this).val(max);
                    amount = max;
                }
                var collection_type = $('#collection_type').val();
                var sales_id = $(this).data('id');
                if (collection_type == 'Adjust') {
                    var total_amount = 0;
                    $('.sales_id:not("#' + sales_id + '"):checked').each(function(index, value) {
                        var id = $(this).val();
                        total_amount += +$('#amount_' + id).val();
                    });
                    var balance = +$('#advance').val() - total_amount;
                    if (balance <= 0) {
                        $('#amount_' + sales_id).val(0);
                        $('#due_' + sales_id).val(max);
                        $('#' + sales_id).prop('checked', false);
                        return false;
                    }
                    if (balance > amount) {
                        $('#amount_' + sales_id).val(amount);
                        $('#due_' + sales_id).val(max - amount);
                    } else {
                        $('#amount_' + sales_id).val(balance);
                        $('#due_' + sales_id).val(max - balance);
                    }
                    $('#' + sales_id).prop('checked', true);
                } else {
                    $('#due_' + sales_id).val(max - amount);
                    $('#' + sales_id).prop('checked', true);
                }
                calculate();
            });

            $(document).on('click', '.sales_id', function(e) {
                var balance = +$('#advance').val() - +$('#total_amount').val();
                var collection_type = $('#collection_type').val();

                var sales_id = $(this).val();
                var payable = $(this).data('payable');
                if ($(this).prop('checked')) {
                    if (collection_type == 'Adjust') {
                        if (balance <= 0) {
                            $(this).prop('checked', false);
                            return false;
                        }
                        if (balance > payable) {
                            $('#amount_' + sales_id).val(payable);
                            $('#due_' + sales_id).val(0);
                        } else {
                            $('#amount_' + sales_id).val(balance);
                            $('#due_' + sales_id).val(payable - balance);
                        }
                    } else {
                        $('#amount_' + sales_id).val(payable);
                        $('#due_' + sales_id).val(0);
                    }
                } else {
                    $('#amount_' + sales_id).val(0);
                    $('#due_' + sales_id).val(payable);
                }
                calculate();
            });

            function calculate() {
                var total_amount = 0;
                $('.sales_id:checked').each(function(index, value) {
                    var sales_id = $(this).val();
                    total_amount += +$('#amount_' + sales_id).val();
                });
                $('#total_amount').val(total_amount);
                if ($('.sales_id').length > 0 && $('.sales_id').length == $('.sales_id:checked').length) {
                    $('#checkAll').prop('checked', true);
                } else {
                    $('#checkAll').prop('checked', false);
                }
            }
        });
    </script>
@endpush
