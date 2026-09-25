@extends('layouts.errors.app')
@section('title', __('402 Payment Required'))
@section('content')
    <div class="error-code">402</div>
    <div class="error-message">Payment Required</div>
    <div class="error-description">
        Access to this resource requires payment.<br>
        Please complete your payment to continue.
    </div>
@endsection
