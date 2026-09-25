@extends('layouts.admin.edit_app')

@section('content')
    <div class="row g-3">
        <div class="col-sm-6">
            <label for="region_id" class="form-label"><b>Region <span class="text-danger">*</span></b></label>
            <select name="region_id" id="region_id" class="form-select select" data-placeholder="Select Region" required>
                <option value=""></option>
                @foreach ($additionalData['regions'] as $item)
                    <option value="{{ $item->id }}"
                        {{ old('region_id', $data->region_id) == $item->id ? 'selected' : '' }}>
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
                    <option value="{{ $item->id }}" {{ old('area_id', $data->area_id) == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6">
            <label for="code" class="form-label"><b>Code</b></label>
            <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $data->code) }}"
                placeholder="Code">
        </div>
        <div class="col-sm-6">
            <label for="name" class="form-label"><b>Name <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $data->name) }}"
                placeholder="Name" required>
        </div>
        <div class="col-sm-6">
            <label for="incharge" class="form-label"><b>Incharge</b></label>
            <input type="text" class="form-control" id="incharge" name="incharge"
                value="{{ old('incharge', $data->incharge) }}" placeholder="Incharge">
        </div>
        <div class="col-sm-6">
            <label for="phone" class="form-label"><b>Phone</b></label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $data->phone) }}"
                placeholder="Phone">
        </div>
        <div class="col-sm-6">
            <label for="email" class="form-label"><b>Email</b></label>
            <input type="email" class="form-control" id="email" name="email"
                value="{{ old('email', $data->email) }}" placeholder="Email">
        </div>
        <div class="col-sm-6">
            <label for="address" class="form-label"><b>Address</b></label>
            <input type="text" class="form-control" id="address" name="address"
                value="{{ old('address', $data->address) }}" placeholder="Address">
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('change', '#region_id', function() {
                var region_id = $(this).val();
                $('#area_id option').remove();
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
        });
    </script>
@endpush
