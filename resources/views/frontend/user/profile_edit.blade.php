@extends('layouts.frontend.app')

@section('content')
<div class="container py-5">
    <div class="row">

        {{-- SIDEBAR --}}
        @include('frontend.user.userSideBar')

        {{-- MAIN CONTENT --}}
        <div class="col-lg-9">

            <div class="card shadow-sm">
                <div class="card-body">

                    <h4 class="mb-4 text-center">Update Profile</h4>

                    <form action="{{ route('user.profile.update') }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="row g-4">

                            {{-- LEFT COLUMN : PROFILE IMAGE --}}
                            <div class="col-md-4 text-center">

                                <img
                                    src="{{ auth()->user()->image
                                        ? asset(auth()->user()->image)
                                        : asset('frontend/images/user/user.png') }}"
                                    class="rounded-circle mb-3"
                                    width="140"
                                    height="140"
                                    style="object-fit:cover">

                                <div class="mb-2">
                                    <input type="file"
                                           name="image"
                                           class="form-control">
                                </div>

                                <small class="text-muted d-block">
                                    JPG / PNG (Max 2MB)
                                </small>

                            </div>

                            {{-- RIGHT COLUMN : FORM FIELDS --}}
                            <div class="col-md-8">

                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text"
                                           name="name"
                                           value="{{ old('name', auth()->user()->name) }}"
                                           class="form-control"
                                           required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email"
                                           name="email"
                                           value="{{ old('email', auth()->user()->email) }}"
                                           class="form-control"
                                           required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text"
                                           name="phone"
                                           value="{{ old('phone', auth()->user()->phone) }}"
                                           class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <input type="text"
                                           name="address"
                                           placeholder="House#12, Road#04, Block#C, Badda, Dhaka"
                                           value="{{ old('address', auth()->user()->address) }}"
                                           class="form-control">
                                </div>

                            </div>

                        </div>

                        <div class="text-end mt-4">
                            <button class="btn btn-primary px-5">
                                Update Profile
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
