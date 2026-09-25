@extends('layouts.admin.create_app')

@section('content')
    <div class="row g-3">
        <div class="col-sm-6">
            <label for="code" class="form-label"><b>Code</b></label>
            <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}"
                placeholder="Code">
        </div>
        <div class="col-sm-6">
            <label for="name" class="form-label"><b>Store Name <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                placeholder="Store Name" required>
        </div>
        <div class="col-sm-6">
            <label for="address" class="form-label"><b>Address</b></label>
            <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}"
                placeholder="Address">
        </div>
        <div class="col-sm-6">
            <label for="remarks" class="form-label"><b>Remarks</b></label>
            <input type="text" class="form-control" id="remarks" name="remarks" value="{{ old('remarks') }}"
                placeholder="Remarks">
        </div>
        <div class="col-12">
            <label for="type" class="form-label"><b>Store Types</b></label>
            <div class="d-flex flex-wrap gap-2">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="type1" name="type[]" value="Product Stock"
                        {{ is_array(old('type')) && in_array('Product Stock', old('type')) ? 'checked' : '' }}>
                    <label class="form-check-label" for="type1">Product Stock</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="type4" name="type[]" value="Damage Stock"
                        {{ is_array(old('type')) && in_array('Damage Stock', old('type')) ? 'checked' : '' }}>
                    <label class="form-check-label" for="type4">Damage Stock</label>
                </div>
            </div>
        </div>
    </div>
@endsection
