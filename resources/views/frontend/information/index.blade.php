@extends('layouts.frontend.app')

@section('content')
    <div class="hero-section" style="margin-bottom:20px;">
        <div class="container">
          <div class="section-card">
                <div class="section-header mb-3">
                    <h5 class="section-title text-center"> {{ $info->name }}</h5>
                </div>  
                
                    <p class="text-justify"> {!! $info->description !!}</p>
                    <br><br><br><br><br><br><br><br><br><br>
          </div>
        </div>
    </div> 
@endsection
