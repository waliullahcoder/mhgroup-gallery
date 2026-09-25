@extends('layouts.errors.app')
@section('title', '419 Page Expired')
@section('content')
    <div class="error-code">419</div>
    <div class="error-message">Page Expired</div>
    <div class="error-description">
        Your session has expired due to inactivity or security reasons.<br>
        Please refresh the page or login again.</div>
    <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">Go Back</a>
@endsection
