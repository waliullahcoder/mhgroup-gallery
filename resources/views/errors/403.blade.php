@extends('layouts.errors.app')
@section('title', __('Unauthorized'))
@section('content')
    <div class="error-code">404</div>
    <div class="error-message">Page Not Found</div>
    <div class="error-description">Sorry, the page you are looking for doesn’t exist or has been moved.</div>
    <a href="{{ url('/') }}" class="home-link">Back to Home</a>
@endsection
