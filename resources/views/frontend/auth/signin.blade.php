@extends('layouts.frontend.app')

@section('content')

<div class="auth-page py-5 animate__animated animate__fadeInTopLeft">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-5">

                <div class="card shadow-sm">

                    <div class="card-body p-4">

                        <h4 class="text-center mb-3">
                            Login to your account
                        </h4>

                        <p class="text-center text-muted mb-4">
                            Welcome back! Please login to continue
                        </p>


                        {{-- NORMAL LOGIN --}}
                        <form method="POST" action="">

                            @csrf

                            <!-- MOBILE -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Mobile No.
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    placeholder="01XXXXXXXXX"
                                    required
                                >

                                @error('phone')
                                    <span class="text-danger small">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>


                            <!-- PASSWORD -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Password
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    required
                                >

                                @error('password')
                                    <span class="text-danger small">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>


                            <!-- REMEMBER + FORGOT -->
                            <div class="d-flex justify-content-between mb-3">

                                <div>

                                    <input
                                        type="checkbox"
                                        name="remember"
                                        id="remember"
                                    >

                                    <label for="remember">
                                        Remember me
                                    </label>

                                </div>

                                <a
                                    href=""
                                    class="small"
                                >
                                    Forgot password?
                                </a>

                            </div>


                            <!-- LOGIN -->
                            <button
                                type="submit"
                                class="btn btn-danger w-100"
                            >
                                Login
                            </button>

                        </form>


                        {{-- SOCIAL LOGIN --}}
                        <div class="text-center my-4">

                            <div class="position-relative">

                                <hr>

                                <span
                                    class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted"
                                >
                                    OR
                                </span>

                            </div>

                        </div>


                        <!-- GOOGLE -->
                        <a
                            href=""
                            class="btn btn-outline-danger w-100 mb-2"
                            style="height:45px;"
                        >

                            <i class="fab fa-google me-2"></i>

                            Continue with Google

                        </a>


                        <!-- FACEBOOK -->
                        <a
                            href=""
                            class="btn btn-primary w-100"
                            style="height:45px;"
                        >

                            <i class="fab fa-facebook-f me-2"></i>

                            Continue with Facebook

                        </a>


                        <!-- SIGN UP -->
                        <p class="text-center mt-3 mb-0">

                            Don't have an account?

                            <a
                                href="{{ route('auth.signupPage') }}"
                                style="color:#1a8961"
                            >
                                Sign up
                            </a>

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection