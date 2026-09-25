@extends('layouts.admin.app')

@push('plugin')
    <link rel="stylesheet" href="{{ asset('backend/css/jquery.minicolors.css') }}">
@endpush

@section('content')
    <form action="{{ Route('admin.admin-settings.update', '0') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex flex-row align-items-center justify-content-between pe-2 py-2">
                        <h6 class="h6 mb-0 py-5px">Admin Settings</h6>
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4 col-6">
                                <label for="title" class="form-label"><b>Title</b></label>
                                <input type="text" id="title" name="title" placeholder="Ttitle"
                                    class="form-control" value="{{ @$data->title }}" required>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <label for="primary_color" class="form-label"><b>Primary Color</b></label>
                                <input type="text" id="primary_color" name="primary_color" placeholder="Primary Color"
                                    class="form-control color" value="{{ @$data->primary_color }}">
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <label for="secondary_color" class="form-label"><b>Secondary Color</b></label>
                                <input type="text" id="secondary_color" name="secondary_color"
                                    placeholder="Secondary Color" class="form-control color"
                                    value="{{ @$data->secondary_color }}">
                            </div>
                            <div class="col-md-4 col-6">
                                <label for="logo" class="form-label"><b>Logo</b></label>
                                <input type="file" id="logo" name="logo" class="form-control" accept="image/*"
                                    {{ file_exists(@$data->logo) ? '' : 'required' }}>
                                @if (file_exists(@$data->logo))
                                    <div class="pt-2">
                                        <img src="{{ asset($data->logo) }}" height="30" alt="Logo">
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4 col-6">
                                <label for="small_logo" class="form-label"><b>Small Logo</b></label>
                                <input type="file" id="small_logo" name="small_logo" class="form-control"
                                    accept="image/*" {{ file_exists(@$data->small_logo) ? '' : 'required' }}>
                                @if (file_exists(@$data->small_logo))
                                    <div class="pt-2">
                                        <img src="{{ asset($data->small_logo) }}" height="30" alt="Small Logo">
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4 col-6">
                                <label for="favicon" class="form-label"><b>Favicon</b></label>
                                <input type="file" id="favicon" name="favicon" class="form-control" accept="image/*"
                                    {{ file_exists(@$data->favicon) ? '' : 'required' }}>
                                @if (file_exists(@$data->favicon))
                                    <div class="pt-2">
                                        <img src="{{ asset($data->favicon) }}" height="30" alt="Favicon">
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="facebook" class="form-label"><b>Facebook</b></label>
                                <input type="text" id="facebook" name="facebook" placeholder="Facebook"
                                    class="form-control" value="{{ @$data->facebook }}">
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="twitter" class="form-label"><b>Twitter</b></label>
                                <input type="text" id="twitter" name="twitter" placeholder="Twitter"
                                    class="form-control" value="{{ @$data->twitter }}">
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="linkedin" class="form-label"><b>Linkedin</b></label>
                                <input type="text" id="linkedin" name="linkedin" placeholder="Linkedin"
                                    class="form-control" value="{{ @$data->linkedin }}">
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="whatsapp" class="form-label"><b>Whatsapp</b></label>
                                <input type="text" id="whatsapp" name="whatsapp" placeholder="Whatsapp"
                                    class="form-control" value="{{ @$data->whatsapp }}">
                            </div>
                            <div class="col-12">
                                <label for="invest_value" class="form-label"><b>Invest Value</b></label>
                                <input type="text" id="invest_value" name="invest_value" placeholder="Invest Value"
                                    class="form-control" value="{{ @$data->invest_value }}" required>
                            </div>
                            <div class="col-12">
                                <label for="footer_text" class="form-label"><b>Footer Text</b></label>
                                <input type="text" id="footer_text" name="footer_text" placeholder="Footer Text"
                                    class="form-control" value="{{ @$data->footer_text }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer pe-2 py-2 text-end">
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('js')
    <script type="text/javascript" src="{{ asset('backend/js/jquery.minicolors.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            if ($('.color').length) {
                $(".color").each(function() {
                    $(this).minicolors();
                });
            }
        });
    </script>
@endpush
