@extends('layouts.frontend.app')

@section('content')
<div class="auth-page py-5 animate__animated animate__fadeInTopRight">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">

                <div class="card shadow-sm">
                    <div class="card-body p-4">

                        <h4 class="text-center mb-3">Create an account</h4>
                        <p class="text-center text-muted mb-4">
                            Sign up to start shopping with us
                        </p>

                        <form method="POST" action="{{route('user.signupPost')}}">
                            @csrf

                            <!-- NAME -->
                            <div class="mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="name"
                                       placeholder="Ex. Mr. John Doe"
                                       class="form-control"
                                       required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mobile No. <span class="text-danger">*</span></label>
                                <input type="phone"
                                       name="phone"
                                       placeholder="Ex. 01712****5678"
                                       class="form-control"
                                       required>
                            </div>

                            <!-- address -->
                            <div class="mb-3">
                                <label class="form-label">Address <span class="text-danger">*</span></label>
                                <input type="address"
                                       name="address"
                                       placeholder="Ex. H#2, R#5, East Rampura, Dhaka-1219"
                                       class="form-control"
                                       required>
                            </div>

                            <!-- PASSWORD -->
                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password"
                                       name="password"
                                       placeholder="Ex. Mr.#@!123"
                                       class="form-control"
                                       required>
                            </div>

                            <!-- CONFIRM PASSWORD -->
                            <div class="mb-3">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password"
                                       name="password_confirmation"
                                       placeholder="Ex. Mr.#@!123"
                                       class="form-control"
                                       required>
                            </div>

                            <!-- TERMS -->
                            <div class="mb-3">
                                <input type="checkbox" required>
                                <label>
                                    I agree to the
                                    <a href="#">Terms & Conditions</a>
                                </label>
                            </div>

                            <!-- BUTTON -->
                            <button type="submit" class="btn btn-danger w-100">
                                Create Account
                            </button>
                        </form>

                        <p class="text-center mt-3 mb-0">
                            Already have an account?
                            <a href="{{route('auth.signinPage') }}" style="color:#1a8961">Login</a>
                        </p>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
