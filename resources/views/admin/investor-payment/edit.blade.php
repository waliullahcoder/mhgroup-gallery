@extends('layouts.admin.edit_app')
@section('content')
    <div class="row g-3">
        <div class="col-md-3 col-sm-6">
            <label for="investor_id" class="form-label"><b>Investor <span class="text-danger">*</span></b></label>
            <select name="investor_id" id="investor_id" class="select form-select" data-placeholder="Select Investor" required>
                <option value=""></option>
                @foreach ($additionalData['investors'] as $item)
                    <option value="{{ $item->id }}"
                        {{ old('investor_id', $data->investor_id) == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-sm-6">
            <label for="date" class="form-label"><b>Date <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control date_picker" id="date" name="date"
                value="{{ date('d-m-Y', strtotime(old('date', $data->date))) }}" placeholder="Date" required>
        </div>
        {{-- <div class="col-md-3 col-sm-6">
            <label for="payment_no" class="form-label"><b>Payment No. <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="payment_no" name="payment_no" value="{{ $payment_no }}"
                placeholder="Payment No." readonly required>
        </div> --}}
        <div class="col-md-3 col-sm-6">
            <label for="coa_id" class="form-label"><b>Account Head <span
                        class="text-danger custom_required">{{ old('payment_type', $data->payment_type) != 'Adjust' ? '*' : '' }}</span></b></label>
            <select name="coa_id" id="coa_id" class="select form-select" data-placeholder="Select Account Head"
                {{ old('payment_type', $data->payment_type) != 'Adjust' ? 'required' : '' }}>
                <option value=""></option>
                @foreach ($additionalData['cash_heads'] as $item)
                    <option value="{{ $item->id }}" {{ old('coa_id', $data->coa_id) == $item->id ? 'selected' : '' }}>
                        {{ $item->head_name . ' - ' . $item->head_code }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-sm-6">
            <label for="payment_type" class="form-label"><b>Payment Type <span class="text-danger">*</span></b></label>
            <select name="payment_type" id="payment_type" class="select form-select" required>
                <option value="Payment" {{ old('payment_type', $data->payment_type) == 'Payment' ? 'selected' : '' }}>
                    Payment</option>
                <option value="Advance" {{ old('payment_type', $data->payment_type) == 'Advance' ? 'selected' : '' }}>
                    Advance</option>
                <option value="Adjust" {{ old('payment_type', $data->payment_type) == 'Adjust' ? 'selected' : '' }}>Adjust
                </option>
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
            <label for="total_amount" class="form-label text-nowrap"><b>Total Payment</b></label>
            <input type="text" class="form-control" id="total_amount" name="total_amount" value="{{ $data->amount }}"
                placeholder="Total Payment">
        </div>
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="bg-primary border-primary text-white text-nowrap">
                        <tr>
                            <th class="text-center px-2" width="30">SL#</th>
                            <th class="px-1">Profit No.</th>
                            <th class="px-1 text-end">Profit Amount</th>
                            <th class="px-1 text-end">Previous Paid</th>
                            <th class="px-1 text-end">Current Paid</th>
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
                        @if ($data->payment_type != 'Advance')
                            @include('admin.investor-payment.partial.edit-rows', [
                                'profits' => $additionalData['profits'],
                                'advance' => $additionalData['advance'],
                                'data' => $data,
                                'payment_type' => $data->payment_type
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
            if ($('.distribution_list_id').length > 0 && $('.distribution_list_id').length == $(
                    '.distribution_list_id:checked').length) {
                $('#checkAll').prop('checked', true);
            } else {
                $('#checkAll').prop('checked', false);
            }

            $(document).on('change', '#investor_id', function() {
                $('#due').val(0);
                $('#advance').val(0);
                $('#tbody tr').remove();
                let payment_type = $('#payment_type').val();
                if (payment_type == 'Payment' || payment_type == 'Adjust') {
                    investorProfit();
                    validation();
                    distribute();
                }
            });

            $(document).on('change', '#payment_type', function(e) {
                let payment_type = $(this).val();
                $('#coa_id').prop('required', true);
                $('.custom_required').text('*');
                if (payment_type == 'Adjust') {
                    $('.custom_required').text('');
                    $('#coa_id').prop('required', false);
                }
                if (payment_type == 'Payment' || payment_type == 'Adjust') {
                    investorProfit();
                    validation();
                    distribute();
                } else {
                    $('#tbody tr').remove();
                }
            });

            function investorProfit() {
                var investor_id = $('#investor_id').val();
                var payment_type = $('#payment_type').val();
                $('#tbody tr').remove();
                $.ajax({
                    url: '{{ request()->fullUrl() }}',
                    type: 'POST',
                    data: {
                        _method: 'GET',
                        investor_id: investor_id,
                        payment_type: payment_type,
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
                    var payment_type = $('#payment_type').val();
                    if (payment_type == 'Adjust') {
                        $('.distribution_list_id').each(function(key, value) {
                            if (advance <= 0) {
                                $(this).prop('checked', false);
                                return true;
                            }
                            var distribution_list_id = $(this).val();
                            var payable = $(this).data('payable');
                            if (advance > payable) {
                                $('#amount_' + distribution_list_id).val(payable);
                                $('#due_' + distribution_list_id).val(0);
                                advance -= payable;
                            } else {
                                $('#amount_' + distribution_list_id).val(advance);
                                $('#due_' + distribution_list_id).val(payable - advance);
                                advance = 0;
                            }
                            $(this).prop('checked', true);
                        });
                    } else {
                        $('.distribution_list_id').each(function(key, value) {
                            var distribution_list_id = $(this).val();
                            var payable = $(this).data('payable');
                            $('#amount_' + distribution_list_id).val(payable);
                            $('#due_' + distribution_list_id).val(0);
                            $(this).prop('checked', true);
                        });
                    }
                } else {
                    $('.distribution_list_id').prop('checked', false);
                    $('.distribution_list_id').each(function(key, value) {
                        var distribution_list_id = $(this).val();
                        var payable = $(this).data('payable');
                        $('#amount_' + distribution_list_id).val(0);
                        $('#due_' + distribution_list_id).val(payable);
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
                var payment_type = $('#payment_type').val();
                if (payment_type == 'Payment' && total_amount > due) {
                    $('#total_amount').val(due);
                }
                if (payment_type == 'Adjust' && total_amount > advance) {
                    $('#total_amount').val(advance);
                }
            }

            function distribute() {
                var total_amount = +$('#total_amount').val();
                $('.distribution_list_id').each(function() {
                    var distribution_list_id = $(this).val();
                    var payable = $(this).data('payable');
                    if (total_amount > 0) {
                        if (payable > total_amount) {
                            $('#amount_' + distribution_list_id).val(total_amount);
                            $('#due_' + distribution_list_id).val(payable - total_amount);
                            total_amount = 0;
                        } else {
                            $('#amount_' + distribution_list_id).val(payable);
                            $('#due_' + distribution_list_id).val(0);
                            total_amount -= payable;
                        }
                        $(this).prop('checked', true);
                    } else {
                        $('#amount_' + distribution_list_id).val(0);
                        $('#due_' + distribution_list_id).val(payable);
                        $(this).prop('checked', false);
                    }
                });

                if ($('.distribution_list_id').length > 0 && $('.distribution_list_id').length == $(
                        '.distribution_list_id:checked').length) {
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
                var payment_type = $('#payment_type').val();
                var distribution_list_id = $(this).data('id');
                if (payment_type == 'Adjust') {
                    var total_amount = 0;
                    $('.distribution_list_id:not("#' + distribution_list_id + '"):checked').each(function(
                        index, value) {
                        var id = $(this).val();
                        total_amount += +$('#amount_' + id).val();
                    });
                    var balance = +$('#advance').val() - total_amount;
                    if (balance <= 0) {
                        $('#amount_' + distribution_list_id).val(0);
                        $('#due_' + distribution_list_id).val(max);
                        $('#' + distribution_list_id).prop('checked', false);
                        return false;
                    }
                    if (balance > amount) {
                        $('#amount_' + distribution_list_id).val(amount);
                        $('#due_' + distribution_list_id).val(max - amount);
                    } else {
                        $('#amount_' + distribution_list_id).val(balance);
                        $('#due_' + distribution_list_id).val(max - balance);
                    }
                    $('#' + distribution_list_id).prop('checked', true);
                } else {
                    $('#due_' + distribution_list_id).val(max - amount);
                    $('#' + distribution_list_id).prop('checked', true);
                }
                calculate();
            });

            $(document).on('click', '.distribution_list_id', function(e) {
                var balance = +$('#advance').val() - +$('#total_amount').val();
                var payment_type = $('#payment_type').val();

                var distribution_list_id = $(this).val();
                var payable = $(this).data('payable');
                if ($(this).prop('checked')) {
                    if (payment_type == 'Adjust') {
                        if (balance <= 0) {
                            $(this).prop('checked', false);
                            return false;
                        }
                        if (balance > payable) {
                            $('#amount_' + distribution_list_id).val(payable);
                            $('#due_' + distribution_list_id).val(0);
                        } else {
                            $('#amount_' + distribution_list_id).val(balance);
                            $('#due_' + distribution_list_id).val(payable - balance);
                        }
                    } else {
                        $('#amount_' + distribution_list_id).val(payable);
                        $('#due_' + distribution_list_id).val(0);
                    }
                } else {
                    $('#amount_' + distribution_list_id).val(0);
                    $('#due_' + distribution_list_id).val(payable);
                }
                calculate();
            });

            function calculate() {
                var total_amount = 0;
                $('.distribution_list_id:checked').each(function(index, value) {
                    var distribution_list_id = $(this).val();
                    total_amount += +$('#amount_' + distribution_list_id).val();
                });
                $('#total_amount').val(total_amount);
                if ($('.distribution_list_id').length > 0 && $('.distribution_list_id').length == $(
                        '.distribution_list_id:checked')
                    .length) {
                    $('#checkAll').prop('checked', true);
                } else {
                    $('#checkAll').prop('checked', false);
                }
            }
        });
    </script>
@endpush
