@extends('layouts.errors.app')
@section('title', '500 Internal Server Error')
@section('content')
    <div class="error-code">500</div>
    <div class="error-message">Internal Server Error</div>
    <div class="error-description">
        Something went wrong on our server.<br>
        Please try again later or contact support if the issue persists.
    </div>
    <a href="{{ url('/') }}" class="btn btn-primary mt-3">Back to Home</a>
@endsection
