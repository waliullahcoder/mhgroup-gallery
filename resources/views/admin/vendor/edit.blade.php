@extends('layouts.admin.edit_app')

@section('content')
    <div class="row g-3">
        <div class="col-md-4 col-sm-6">
            <label for="code" class="form-label"><b>Code</b></label>
            <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $data->code) }}"
                placeholder="Code">
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="name" class="form-label"><b>Vendor Name <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $data->name) }}"
                placeholder="Vendor Name" required>
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="contact_person" class="form-label"><b>Contact Person</b></label>
            <input type="text" class="form-control" id="contact_person" name="contact_person"
                value="{{ old('contact_person', $data->contact_person) }}" placeholder="Contact Person">
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="email" class="form-label"><b>Contact Email</b></label>
            <input type="email" class="form-control" id="email" name="email"
                value="{{ old('email', $data->email) }}" placeholder="Contact Email">
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="phone" class="form-label"><b>Contact Number</b></label>
            <input type="text" class="form-control" id="phone" name="phone"
                value="{{ old('phone', $data->phone) }}" placeholder="Contact Number">
        </div>
        <div class="col-md-4 col-sm-6">
            <label for="address" class="form-label"><b>Contact Address</b></label>
            <input type="text" class="form-control" id="address" name="address"
                value="{{ old('address', $data->address) }}" placeholder="Contact Address">
        </div>
    </div>
@endsection
