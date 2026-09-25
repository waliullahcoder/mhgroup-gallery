<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ $admin_setting->title ?? config('app.name') }}</title>
    <link rel="shortcut icon"
        href="{{ asset(file_exists($admin_setting->favicon ?? '') ? $admin_setting->favicon : 'backend/images/logo/favicon.png') }}"
        type="image/x-icon" />

    @include('layouts.admin.partial.styles')
    @include('layouts.admin.partial.alert')
</head>

<body class="bg-light">
    <div class="overflow-hidden site-wrapper {{ Session::get('session-sidebar') }}">
        @include('layouts.admin.partial.sidebar')

        <div class="content-wrapper">
            @include('layouts.admin.partial.header')

            <div class="content">
                <div class="p-sm-4 p-3">
                    <div class="row g-3">
                        <div class="col-12">
                            @php
                                $route = \Request::route()->getName();
                                $storeRoute = str_replace('create', 'store', $route);
                                $indexRoute = str_replace('create', 'index', $route);
                            @endphp

                            <form action="{{ route($storeRoute) }}" method="POST" enctype="multipart/form-data"
                                @isset($target_blank) target="_blank" @endisset id="store_form"
                                {{ $validation ?? '' }}>
                                @csrf

                                <div class="card">
                                    <div class="card-header pe-2 py-2">
                                        <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center">
                                            <h6 class="mb-0 text-uppercase flex-grow-1">
                                                {{ $title ?? 'Please Set Title' }}</h6>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route($indexRoute) }}" class="btn btn-primary btn-sm">Go
                                                    Back</a>
                                                <button type="submit" class="btn btn-primary btn-sm submit_btn">
                                                    <span class="btn-spiner spinner-border spinner-border-sm"
                                                        style="display: none;" aria-hidden="true"></span>
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        @yield('content')
                                    </div>

                                    <div class="card-footer text-end pe-2 py-2">
                                        <button type="submit" class="btn btn-primary btn-sm submit_btn">
                                            <span class="btn-spiner spinner-border spinner-border-sm"
                                                style="display: none;" aria-hidden="true"></span>
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @include('layouts.admin.partial.footer')
        </div>
    </div>

    @include('layouts.admin.partial.scripts')

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    @stack('js')
</body>

</html>
