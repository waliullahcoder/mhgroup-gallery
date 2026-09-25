@extends('layouts.admin.app')

@section('content')
    <form action="{{ route('admin.settings.update', 0) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header py-2 pe-2 d-flex flex-row justify-content-between align-items-center">
                        <h6 class="mb-0 text-uppercase">Update Settings</h6>
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            {{-- Text Inputs --}}
                            @php
                                $fields = [
                                    ['app_name', 'App Name', 'text', 'col-lg-3 col-md-4 col-6', true],
                                    ['title', 'Title', 'text', 'col-lg-3 col-md-4 col-6', true],
                                    ['primary_phone', 'Primary Phone', 'text', 'col-lg-3 col-md-4 col-6', true],
                                    ['secondary_phone', 'Secondary Phone', 'text', 'col-lg-3 col-md-4 col-6', false],
                                    ['primary_email', 'Primary Email', 'email', 'col-lg-3 col-md-4 col-6', true],
                                    ['secondary_email', 'Secondary Email', 'email', 'col-lg-3 col-md-4 col-6', false],
                                    ['address', 'Address', 'text', 'col-lg-3 col-md-4 col-6', false],
                                    ['meta_title', 'Meta Title', 'text', 'col-lg-3 col-md-4 col-6', false],
                                    ['meta_keyword', 'Meta Keyword', 'text', 'col-lg-3 col-md-4 col-6', false],
                                ];
                            @endphp

                            @foreach ($fields as $field)
                                @php
                                    [$name, $label, $type, $class, $required] = $field;
                                @endphp
                                <div class="{{ $class }}">
                                    <label for="{{ $name }}" class="form-label">
                                        <b>{{ $label }}
                                            @if ($required)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </b>
                                    </label>
                                    <input type="{{ $type }}" class="form-control" id="{{ $name }}"
                                        name="{{ $name }}" value="{{ old($name, $data->$name ?? '') }}"
                                        placeholder="{{ $label }}" @if ($required) required @endif>
                                </div>
                            @endforeach
                            {{-- Meta Description --}}
                            <div class="col-lg-9 col-sm-6">
                                <label for="meta_description" class="form-label"><b>Meta Description</b></label>
                                <textarea name="meta_description" id="meta_description" class="form-control" rows="1"
                                    placeholder="Meta Description">{{ old('meta_description', $data->meta_description ?? '') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label"><b>Footer Description</b></label>
                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Footer Description">{{ old('description', $data->description ?? '') }}</textarea>
                            </div>

                            {{-- Google Map --}}
                            <div class="col-12">
                                <label for="google_map" class="form-label"><b>Google Map</b></label>
                                <textarea name="google_map" id="google_map" class="form-control" rows="3" placeholder="Google Map">{{ old('google_map', $data->google_map ?? '') }}</textarea>
                            </div>

                            <div class="col-sm-6">
                                <label for="sms_api_url" class="form-label"><b>SMS API Url</b></label>
                                <input type="text" class="form-control" id="sms_api_url" name="sms_api_url"
                                    value="{{ $data->sms_api_url ?? '' }}" placeholder="SMS API Url">
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <label for="sms_api_key" class="form-label"><b>SMS API Key</b></label>
                                <input type="text" class="form-control" id="sms_api_key" name="sms_api_key"
                                    value="{{ $data->sms_api_key ?? '' }}" placeholder="SMS API Key">
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <label for="sms_api_id" class="form-label"><b>API User ID</b></label>
                                <input type="text" class="form-control" id="sms_api_id" name="sms_api_id"
                                    value="{{ $data->sms_api_id ?? '' }}" placeholder="API User ID">
                            </div>
                            {{-- Image Uploads --}}
                            @php
                                $images = [
                                    ['favicon', 'Favicon', 30],
                                    ['logo', 'Logo', 30],
                                    // ['footer_logo', 'Footer Logo', 30],
                                    ['placeholder', 'Placeholder', 30],
                                    ['meta_image', 'Meta Image', 30],
                                ];
                            @endphp

                            @foreach ($images as [$name, $label, $height])
                                <div class="col-lg-3 col-sm-6">
                                    <label for="{{ $name }}" class="form-label"><b>{{ $label }}</b></label>
                                    <input class="form-control" type="file" id="{{ $name }}"
                                        name="{{ $name }}" accept="image/*">
                                    @if (!empty($data->$name) && file_exists(public_path($data->$name)))
                                        <div>
                                            <img class="mt-1" src="{{ asset($data->$name) }}"
                                                height="{{ $height }}" alt="{{ $label }}">
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            <div class="col-lg-3 col-sm-6">
                                <label for="banner_one" class="form-label"><b>Home Banner</b></label>
                                <input type="file" name="banner_one" id="banner_one" class="form-control"
                                    accept="image/*">
                                @if (!empty($data->banner_one) && file_exists(public_path($data->banner_one)))
                                    <div>
                                        <img class="mt-1" src="{{ asset($data->banner_one) }}" height="30"
                                            alt="Banner Image">
                                    </div>
                                @endif
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <label for="banner_one_link" class="form-label"><b>Home Banner Link</b></label>
                                <input type="text" name="banner_one_link" id="banner_one_link" class="form-control"
                                    placeholder="Home Banner Link"
                                    value="{{ old('banner_one_link', $data->banner_one_link ?? '') }}">
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <label for="banner_one_status" class="form-label"><b>Home Banner Status</b></label>
                                <select name="banner_one_status" id="banner_one_status" class="form-select select"
                                    data-placeholder="Select Status" required>
                                    <option value="1"
                                        {{ old('banner_one_status', $data->banner_one_status ?? '') == '1' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0"
                                        {{ old('banner_one_status', $data->banner_one_status ?? '') == '0' ? 'selected' : '' }}>
                                        Deactive</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <label for="page_heading_bg" class="form-label"><b>Page Header BG</b></label>
                                <input type="file" name="page_heading_bg" id="page_heading_bg" class="form-control"
                                    accept="image/*">
                                @if (!empty($data->page_heading_bg) && file_exists(public_path($data->page_heading_bg)))
                                    <div>
                                        <img class="mt-1" src="{{ asset($data->page_heading_bg) }}" height="30"
                                            alt="Page Header BG">
                                    </div>
                                @endif
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <label for="banner_two" class="form-label"><b>Home Banner</b></label>
                                <input type="file" name="banner_two" id="banner_two" class="form-control"
                                    accept="image/*">
                                @if (!empty($data->banner_two) && file_exists(public_path($data->banner_two)))
                                    <div>
                                        <img class="mt-1" src="{{ asset($data->banner_two) }}" height="30"
                                            alt="Banner Image">
                                    </div>
                                @endif
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <label for="banner_two_link" class="form-label"><b>Home Banner Link</b></label>
                                <input type="text" name="banner_two_link" id="banner_two_link" class="form-control"
                                    placeholder="Home Banner Link"
                                    value="{{ old('banner_two_link', $data->banner_two_link ?? '') }}">
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <label for="banner_two_status" class="form-label"><b>Home Banner Status</b></label>
                                <select name="banner_two_status" id="banner_two_status" class="form-select select"
                                    data-placeholder="Select Status" required>
                                    <option value="1"
                                        {{ old('banner_two_status', $data->banner_two_status ?? '') == '1' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0"
                                        {{ old('banner_two_status', $data->banner_two_status ?? '') == '0' ? 'selected' : '' }}>
                                        Deactive</option>
                                </select>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <label for="tax" class="form-label"><b>Tax(%)</b></label>
                                <input type="text" name="tax" id="tax" class="form-control"
                                    placeholder="Tax(%)"
                                    value="{{ old('tax', $data->tax ?? '') }}">
                            </div>

                            <div class="col-lg-6 col-sm-6">
                                <label for="discount_type" class="form-label"><b>Discount Type</b></label>
                                <select name="discount_type" id="banner_two_status" class="form-select" required>
                                    <option value="percent"
                                        {{ $data->discount_type == 'percent' ? 'selected' : '' }}>
                                        Percent</option>
                                    <option value="amount"
                                        {{ $data->discount_type == 'amount' ? 'selected' : '' }}>
                                        Amount</option>
                                    
                                </select>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <label for="discount" class="form-label"><b>Discount</b></label>
                                <input type="text" name="discount" id="discount" class="form-control"
                                    placeholder="Discount Value"
                                    value="{{ old('discount', $data->discount ?? '') }}">
                            </div>

                            {{-- Social Links --}}
                            <div class="col-12">
                                <label for="socialLinks" class="form-label"><b>Social Links</b></label>
                                <div class="row gx-3 gy-2">
                                    @php
                                        $socials = [
                                            ['facebook_page', '#1877F2', 'facebook-f'],
                                            ['youtube', '#FF0000', 'youtube'],
                                            ['twitter', '#1D9BF0', 'twitter'],
                                            ['instagram', '#0077B7', 'instagram'],
                                            ['pinterest', '#E33E2B', 'pinterest'],
                                            ['whatsapp', '#47C756', 'whatsapp'],
                                        ];
                                    @endphp

                                    @foreach ($socials as [$name, $color, $icon])
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <span class="input-group-text text-white justify-content-center"
                                                    style="background-color: {{ $color }}; width: 42px;">
                                                    <i class="fab fa-{{ $icon }}"></i>
                                                </span>
                                                <input type="text" class="form-control" name="{{ $name }}"
                                                    placeholder="https://www."
                                                    value="{{ old($name, $data->$name ?? '') }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card-footer text-end py-2 pe-2">
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
