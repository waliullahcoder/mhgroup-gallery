@extends('layouts.frontend.app')

@section('content')
<div class="auth-page py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        @if($user==null)
                        <h4 class="text-center mb-3">Input only registered mobile number</h4>
                        <p class="text-center text-muted mb-4">
                            You will get password reset form after set the correct number.
                        </p>
                        @endif
                        
                       <form method="post" action="{{ route('user.forgotPasswordPost') }}">
                            @csrf

                            @if($user)
                            <input type="hidden" name="phone" value="{{$user->phone}}">
                             <div class="mb-3">
                                <label class="form-label">Set New Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters, can include letters, numbers or any special characters."required>
                            </div>
                             <button type="submit" class="btn btn-primary">Change Password Confirm</button>
                            @else
                            <div class="mb-3">
                                <label class="form-label">Mobile No.</label>
                                <input type="text" name="phone" class="form-control" placeholder="01XXXXXXXXX" required>
                            </div>
                             <button type="submit" class="btn btn-primary">Sent Mobile Number</button>
                            @endif
                            
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection