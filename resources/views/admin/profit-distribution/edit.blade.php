@extends('layouts.admin.edit_app')

@section('content')
    <div class="row g-3">
        <div class="col-md-3 col-sm-6">
            <label for="date" class="form-label"><b>Date <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control date_picker" id="date" name="date"
                value="{{ date('d-m-Y', strtotime(old('date', $data->date))) }}" placeholder="Date" required>
        </div>
        <div class="col-md-3 col-sm-6">
            <label for="month" class="form-label"><b>Month <span class="text-danger">*</span></b></label>
            <select class="form-select select" name="month" id="month" data-placeholder="Select Month." required>
                @php
                    $months = [
                        'January',
                        'February',
                        'March',
                        'April',
                        'May',
                        'June',
                        'July',
                        'August',
                        'September',
                        'October',
                        'November',
                        'December',
                    ];
                @endphp
                @foreach ($months as $item)
                    <option value="{{ $item }}" {{ request('month', $data->month) == $item ? 'selected' : '' }}>
                        {{ $item }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-sm-6">
            <label for="year" class="form-label"><b>Year <span class="text-danger">*</span></b></label>
            <select class="form-select select" name="year" id="year" data-placeholder="Select Year." required>
                @for ($i = 2015; $i <= 2055; $i++)
                    <option value="{{ $i }}" {{ request('year', $data->year) == $i ? 'selected' : '' }}>
                        {{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-3 col-sm-6">
            <label for="product_id" class="form-label"><b>Books <span class="text-danger">*</span></b></label>
            <select name="product_id" id="product_id" class="form-select select" data-placeholder="Select Book" required>
                <option value=""></option>
                @foreach (\App\Models\Product::where('status', true)->orderBy('name', 'asc')->get() as $item)
                    <option value="{{ $item->id }}" {{ $data->product_id == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <div id="response">
                @include('admin.profit-distribution.partial.data', [
                    'detailData' => $additionalData['detailData'],
                ])
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('change', '#year,#month', function(e) {
                $('#response').html('');
                var year = $('#year').val();
                var month = $('#month').val();
                $.ajax({
                    url: '{{ request()->fullUrl() }}',
                    type: 'POST',
                    data: {
                        _method: 'GET',
                        year: year,
                        month: month
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#response').html(response.data);
                        }
                    }
                });
            });
        });
    </script>
@endpush
