<link href="{{ asset('backend/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
<link href="{{ asset('backend/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
<link href="{{ asset('backend/css/buttons.dataTables.min.css') }}" rel="stylesheet" />
<link href="{{ asset('backend/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" />

<link rel="stylesheet" href="{{ asset('frontend/css/fonts.googleapis.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/jquery.fancybox.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/animate.min.css') }}">
@notifyCss
@stack('plugin')
<link rel="stylesheet" href="{{ asset('backend/css/plugin.css') }}">
<style>
    :root {
        --bs-primary: {{ @$admin_setting->primary_color ?? '#249d79' }};
        --bs-secondary: {{ @$admin_setting->secondary_color ?? '#415FFF' }};
    }
</style>
<!-- <link rel="stylesheet" href="{{ asset('frontend/css/custom.css') }}"> -->
<link rel="stylesheet" href="{{ asset('backend/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/responsive.css') }}">
@stack('css')
