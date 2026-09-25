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
                                $routeName = \Request::route()->getName();
                                $updateRoute = str_replace('.edit', '.update', $routeName);
                                $backRoute = str_replace(['.edit', '.show'], '.index', $routeName);
                            @endphp

                            <form action="{{ route($updateRoute, $data->id ?? null) }}" method="POST" id="update_form"
                                enctype="multipart/form-data" {{ $additionalData['validation'] ?? '' }}>
                                @csrf
                                @method('PUT')

                                <div class="card">
                                    <div class="card-header pe-2 py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-uppercase">{{ $title ?? 'Please Set Title' }}</h6>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route($backRoute) }}" class="btn btn-primary btn-sm">Go
                                                    Back</a>
                                                <button type="submit"
                                                    class="btn btn-primary btn-sm submit_btn">Update</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        @yield('content')
                                    </div>

                                    <div class="card-footer text-end pe-2 py-2">
                                        <button type="submit" class="btn btn-primary btn-sm submit_btn">Update</button>
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
