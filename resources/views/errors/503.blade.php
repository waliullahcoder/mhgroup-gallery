@extends('layouts.errors.app')
@section('title', '503 Service Unavailable')
@section('content')
    <div class="error-code">503</div>
    <div class="error-message">Service Unavailable</div>
    <div class="error-description">
        Our servers are currently undergoing maintenance or are temporarily overloaded.<br>
        Please check back in a few minutes.
    </div>
    <a href="{{ url()->current() }}" class="retry-link">Try Again</a>
@endsection
