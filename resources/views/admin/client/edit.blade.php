@extends('layouts.admin.edit_app')

@section('content')
    <div class="row g-3">
        <div class="col-sm-6">
            <label for="code" class="form-label"><b>Code</b></label>
            <input type="text" class="form-control" id="code" name="code" value="{{ $data->code }}"
                placeholder="Code">
        </div>
        <div class="col-sm-6">
            <label for="name" class="form-label"><b>Name <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $data->name }}"
                placeholder="Name" required>
        </div>
        <div class="col-sm-6">
            <label for="region_id" class="form-label"><b>Region <span class="text-danger">*</span></b></label>
            <select name="region_id" id="region_id" class="form-select select" data-placeholder="Select Region" required>
                <option value=""></option>
                @foreach ($additionalData['regions'] as $item)
                    <option value="{{ $item->id }}" {{ $data->region_id == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6">
            <label for="area_id" class="form-label"><b>Area <span class="text-danger">*</span></b></label>
            <select name="area_id" id="area_id" class="form-select select" data-placeholder="Select Area" required>
                <option value=""></option>
                @php
                    $areas = \App\Models\Area::where('region_id', $data->region_id)
                        ->where('status', true)
                        ->orderBy('name', 'asc')
                        ->get();
                    if (!is_null(old('region_id'))) {
                        $areas = \App\Models\Area::where('region_id', old('region_id'))
                            ->where('status', true)
                            ->orderBy('name', 'asc')
                            ->get();
                    }
                @endphp
                @foreach ($areas as $item)
                    <option value="{{ $item->id }}" {{ $data->area_id == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6">
            <label for="territory_id" class="form-label"><b>Territory <span class="text-danger">*</span></b></label>
            <select name="territory_id" id="territory_id" class="form-select select" data-placeholder="Select Territory"
                required>
                <option value=""></option>
                @php
                    $territories = \App\Models\Territory::where('area_id', $data->area_id)
                        ->where('status', true)
                        ->orderBy('name', 'asc')
                        ->get();
                    if (!is_null(old('area_id'))) {
                        $territories = \App\Models\Territory::where('area_id', old('area_id'))
                            ->where('status', true)
                            ->orderBy('name', 'asc')
                            ->get();
                    }
                @endphp
                @foreach ($territories as $item)
                    <option value="{{ $item->id }}" {{ $data->territory_id == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6">
            <label for="contact_person" class="form-label"><b>Contact Person</b></label>
            <input type="text" class="form-control" id="contact_person" name="contact_person"
                value="{{ $data->contact_person }}" placeholder="Contact Person">
        </div>
        <div class="col-sm-6">
            <label for="phone" class="form-label"><b>Phone</b></label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ $data->phone }}"
                placeholder="Phone">
        </div>
        <div class="col-sm-6">
            <label for="email" class="form-label"><b>Email</b></label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $data->email }}"
                placeholder="Email">
        </div>
        <div class="col-sm-6">
            <label for="address" class="form-label"><b>Address</b></label>
            <input type="text" class="form-control" id="address" name="address" value="{{ $data->address }}"
                placeholder="Address">
        </div>
        <div class="col-sm-6">
            <label for="bin_no" class="form-label"><b>BIN No</b></label>
            <input type="text" class="form-control" id="bin_no" name="bin_no" value="{{ $data->bin_no }}"
                placeholder="BIN No">
        </div>
        <div class="col-sm-6">
            <label for="credit_limit" class="form-label"><b>Credit Limit</b></label>
            <input type="number" class="form-control" id="credit_limit" name="credit_limit"
                value="{{ $data->credit_limit }}" placeholder="Credit Limit">
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('change', '#region_id', function() {
                var region_id = $(this).val();
                $('#area_id option').remove();
                $('#territory_id option').remove();
                $.ajax({
                    url: '{{ request()->fullUrl() }}',
                    type: 'POST',
                    data: {
                        _method: 'GET',
                        region_id: region_id,
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#area_id').append('<option value=""></option>');
                            $.each(response.areas, function(key, value) {
                                $('#area_id').append(
                                    `<option value="${value.id}">${value.name}</option>`
                                );
                            });
                        }
                    }
                });
            });

            $(document).on('change', '#area_id', function() {
                var area_id = $(this).val();
                $('#territory_id option').remove();
                $.ajax({
                    url: '{{ request()->fullUrl() }}',
                    type: 'POST',
                    data: {
                        _method: 'GET',
                        area_id: area_id,
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#territory_id').append('<option value=""></option>');
                            $.each(response.territories, function(key, value) {
                                $('#territory_id').append(
                                    `<option value="${value.id}">${value.name}</option>`
                                );
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
