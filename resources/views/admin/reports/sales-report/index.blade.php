@extends('layouts.admin.report_app')

@section('form')
    <div class="row g-3">
        <input type="hidden" name="print" value="">
        <input type="hidden" name="filter" value="1">
        <div class="col-md-4 col-sm-6">
            <label for="report_type" class="form-label"><b>Report Type <span class="text-danger">*</span></b></label>
            <select name="report_type" id="report_type" class="form-select select" data-placeholder="Select Report Type"
                required>
                <option value="invoice_history" {{ request('report_type') == 'invoice_history' ? 'selected' : '' }}>
                    Invoice History</option>
                <option value="product_history" {{ request('report_type') == 'product_history' ? 'selected' : '' }}>
                    Product History</option>
                <option value="product_summary" {{ request('report_type') == 'product_summary' ? 'selected' : '' }}>
                    Product Summary</option>
            </select>
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="date_range" class="form-label"><b>Date <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control date-range" name="date_range" id="date_range"
                placeholder="Select Date Range" data-time-picker="true" data-format="DD-MM-Y"
                data-separator=" to " autocomplete="off" required
                value="{{ !is_null($start_date) && !is_null($end_date) ? date('d-m-Y', strtotime($start_date)) . ' to ' . date('d-m-Y', strtotime($end_date)) : date('01-m-Y') . ' to ' . date('t-m-Y') }}">
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="product_id" class="form-label"><b>Products</b></label>
            <select name="product_id[]" id="product_id" class="form-select select" data-placeholder="Select Products"
                multiple>
                <option value=""></option>
                @foreach ($products as $item)
                    <option value="{{ $item->id }}"
                        {{ is_array(request('product_id')) && in_array($item->id, request('product_id')) ? 'selected' : '' }}>
                        {{ @$item->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
@endsection

@section('content')
    {!! $dataTable->table(['class' => 'dataTable table align-middle'], true) !!}
@endsection

@push('js')
    <script type="text/javascript" src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    {!! $dataTable->scripts() !!}

    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('click', '.getPdf', function(e) {
                e.preventDefault();
                $('input[name="print"]').val('true');
                $('.filter_form')[0].setAttribute("target", "_blank");
                $('.filter_form').submit();
            });

            $(document).on('click', '#filter_btn', function(e) {
                $('input[name="print"]').val('');
                $('.filter_form')[0].setAttribute("target", "_self");
            });
        });
    </script>
@endpush
