@extends('layouts.errors.app')
@section('title', __('Unauthorized'))
@section('content')
    <div class="error-code">401</div>
    <div class="error-message">Unauthorized Access</div>
    <div class="error-description">
        You don't have permission to view this page.<br>
        Please log in with appropriate credentials or contact support.
    </div>
    <a href="{{ route('admin.login') }}" class="home-link">Login</a>
@endsection
