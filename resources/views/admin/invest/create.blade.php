@extends('layouts.admin.create_app')

@section('content')
    <div class="row g-3">
        <div class="col-md-4 col-sm-6">
            <label for="investor_id" class="form-label"><b>Investor <span class="text-danger">*</span></b></label>
            <select name="investor_id" id="investor_id" class="select form-select" data-placeholder="Select Investor" required>
                <option value=""></option>
                @foreach ($investors as $item)
                    <option value="{{ $item->id }}" {{ old('investor_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="coa_id" class="form-label"><b>Cash Account <span class="text-danger">*</span></b></label>
            <select name="coa_id" id="coa_id" class="form-select select" data-placeholder="Select Cash Account"
                required>
                <option value=""></option>
                @foreach ($cashHeads as $item)
                    <option value="{{ $item->id }}" {{ old('coa_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->head_name . ' - ' . $item->head_code }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="date" class="form-label"><b>Date <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control date_picker" id="date" name="date"
                value="{{ date('d-m-Y', strtotime(old('date', date('d-m-Y')))) }}" placeholder="Invest Date" required>
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="product_id" class="form-label"><b>Product <span class="text-danger">*</span></b></label>
            <select name="product_id" id="product_id" class="select form-select" data-placeholder="Select Product" required>
                <option value=""></option>
                @foreach ($products as $item)
                    <option value="{{ $item->id }}" {{ old('product_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="qty" class="form-label"><b>Invest Qty <span class="text-danger">*</span></b></label>
            <input type="number" class="form-control" id="qty" name="qty" value="{{ old('qty') }}"
                placeholder="Invest Qty" required>
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="amount" class="form-label"><b>Amount <span class="text-danger">*</span></b></label>
            <input type="number" class="form-control" id="amount" name="amount" value="{{ old('amount') }}"
                placeholder="Invest Amount" readonly required>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('keyup', '#qty', function(e) {
                var qty = $(this).val();
                var invest_value = "{{ $admin_setting->invest_value }}";
                var amount = qty * invest_value;
                $('#amount').val(amount);
                $('.investAmount').val(amount);
            });
        });
    </script>
@endpush
