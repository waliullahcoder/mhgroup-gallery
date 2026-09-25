@extends('layouts.errors.app')
@section('title', '429 Too Many Requests')
@section('content')
    <div class="error-code">429</div>
    <div class="error-message">Too Many Requests</div>
    <div class="error-description">
        You’ve sent too many requests in a short period.<br>
        Please wait a moment and try again.
    </div>
    <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">Go Back</a>
@endsection
