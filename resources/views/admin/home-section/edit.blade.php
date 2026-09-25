@extends('layouts.admin.edit_app')

@section('content')
    <div class="row g-3">
        <div class="col-md-4 col-sm-6">
            <label for="image" class="form-label"><b>Slide Image <span class="text-danger">*</span></b></label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*"
                {{ !file_exists($data->image) ? 'required' : '' }}>
            @if (file_exists($data->image))
                <img class="mt-2" src="{{ asset($data->image) }}" height="30" alt="Image">
            @endif
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="mobile_image" class="form-label"><b>Responsive Image <span class="text-danger">*</span></b></label>
            <input type="file" class="form-control" id="mobile_image" name="mobile_image" accept="image/*"
                {{ !file_exists($data->mobile_image) ? 'required' : '' }}>
            @if (file_exists($data->mobile_image))
                <img class="mt-2" src="{{ asset($data->mobile_image) }}" height="30" alt="Image">
            @endif
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="link" class="form-label"><b>Banner Link</b></label>
            <input type="text" class="form-control" id="link" name="link" value="{{ old('link', $data->link) }}">
        </div>
    </div>
@endsection
