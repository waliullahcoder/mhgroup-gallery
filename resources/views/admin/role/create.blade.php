@extends('layouts.admin.create_app')

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <label for="name" class="form-label require"><b>Name <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                value="{{ old('name') }}" required>
        </div>
    </div>
@endsection
