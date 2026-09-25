@extends('layouts.admin.edit_app')

@section('content')
    <div class="row g-3">
        <div class="col-sm-6">
            <label for="code" class="form-label"><b>Code</b></label>
            <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $data->code) }}"
                placeholder="Code">
        </div>
        <div class="col-sm-6">
            <label for="name" class="form-label"><b>Name <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $data->name) }}"
                placeholder="Name" required>
        </div>
        <div class="col-sm-6">
            <label for="incharge" class="form-label"><b>Incharge</b></label>
            <input type="text" class="form-control" id="incharge" name="incharge"
                value="{{ old('incharge', $data->incharge) }}" placeholder="Incharge">
        </div>
        <div class="col-sm-6">
            <label for="phone" class="form-label"><b>Phone</b></label>
            <input type="text" class="form-control" id="phone" name="phone"
                value="{{ old('phone', $data->phone) }}" placeholder="Phone">
        </div>
        <div class="col-sm-6">
            <label for="email" class="form-label"><b>Email</b></label>
            <input type="email" class="form-control" id="email" name="email"
                value="{{ old('email', $data->email) }}" placeholder="Email">
        </div>
        <div class="col-sm-6">
            <label for="address" class="form-label"><b>Address</b></label>
            <input type="text" class="form-control" id="address" name="address"
                value="{{ old('address', $data->address) }}" placeholder="Address">
        </div>
    </div>
@endsection
