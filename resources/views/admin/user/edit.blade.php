@extends('layouts.admin.edit_app')

@section('content')
    <div class="row g-3">
        <div class="col-lg-4 col-sm-6">
            <label for="role_id" class="form-label require"><b>Role <span class="text-danger">*</span></b></label>
            <select class="form-control select" name="role_id" id="role_id" data-placeholder="select Role" required>
                @foreach ($additionalData['roles'] as $role)
                    @if (!Auth::user()->hasRole('Software Admin') && $role->name == 'System Admin')
                        @continue
                    @endif
                    <option value="{{ $role->id }}" {{ $data->hasRole($role->name) ? 'selected' : '' }}>
                        {{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="name" class="form-label"><b>Name <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                value="{{ $data->name }}" required>
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="user_name" class="form-label"><b>User ID <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="user_name" name="user_name" placeholder="User ID"
                value="{{ $data->user_name }}" required>
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="email" class="form-label"><b>Email</b></label>
            <input type="email" class="form-control" id="email" name="email" placeholder="User Email"
                value="{{ $data->email }}">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="phone" class="form-label"><b>Phone</b></label>
            <input type="text" class="form-control" id="phone" name="phone" placeholder="User Phone"
                value="{{ $data->phone }}">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="image" class="form-label"><b>Image</b></label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
            @if (!empty($data->image) && file_exists(public_path($data->image)))
                <img class="mt-2" src="{{ asset($data->image) }}" height="30" alt="Image">
            @endif
        </div>
    </div>
@endsection
