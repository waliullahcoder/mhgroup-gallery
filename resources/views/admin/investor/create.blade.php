@extends('layouts.admin.create_app')

@section('content')
    <div class="row g-3">
        <div class="col-lg-4 col-sm-6">
            <label for="name" class="form-label"><b>Investor Name <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="name" name="name" required value="{{ old('name') }}"
                placeholder="Investor Name">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="email" class="form-label"><b>Email</b></label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                placeholder="Email">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="phone" class="form-label"><b>Phone No. <span class="text-danger">*</span></b></label>
            <input type="number" class="form-control" id="phone" name="phone" required value="{{ old('phone') }}"
                placeholder="Phone">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="nid" class="form-label"><b>NID No.</b></label>
            <input type="number" class="form-control" id="nid" name="nid" value="{{ old('nid') }}"
                placeholder="NID No.">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="address" class="form-label"><b>Address</b></label>
            <input type="text" name="address" id="address" class="form-control" placeholder="Address"
                value="{{ old('address') }}">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="document" class="form-label"><b>Document</b></label>
            <input type="file" name="document" id="document" class="form-control">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="bkash" class="form-label"><b>Bkash Account</b></label>
            <input type="number" name="bkash" id="bkash" class="form-control" placeholder="Bkash Account"
                value="{{ old('bkash') }}">
        </div>
        {{-- <div class="col-lg-4 col-sm-6">
            <label for="rocket" class="form-label"><b>Rocket Account</b></label>
            <input type="number" name="rocket" id="rocket" class="form-control" placeholder="Rocket Account"
                value="{{ old('rocket') }}">
        </div> --}}
        <div class="col-lg-4 col-sm-6">
            <label for="nagad" class="form-label"><b>Nagad Account</b></label>
            <input type="number" name="nagad" id="nagad" class="form-control" placeholder="Nagad Account"
                value="{{ old('nagad') }}">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="bank" class="form-label"><b>Bank - Branch</b></label>
            <input type="text" name="bank" id="bank" class="form-control" placeholder="Bank Name"
                value="{{ old('bank') }}">
        </div>
        {{-- <div class="col-lg-4 col-sm-6">
            <label for="branch" class="form-label"><b>Branch Name</b></label>
            <input type="text" name="branch" id="branch" class="form-control" placeholder="Branch Name"
                value="{{ old('branch') }}">
        </div> --}}
        <div class="col-lg-4 col-sm-6">
            <label for="account_name" class="form-label"><b>Account Name</b></label>
            <input type="text" name="account_name" id="account_name" class="form-control" placeholder="Account Name"
                value="{{ old('account_name') }}">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="account_no" class="form-label"><b>Account Number</b></label>
            <input type="number" name="account_no" id="account_no" class="form-control" placeholder="Account Number"
                value="{{ old('account_no') }}">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="image" class="form-label"><b>Image</b></label>
            <input type="file" class="form-control" name="image" id="image" accept="image/*">
        </div>
    </div>
@endsection
