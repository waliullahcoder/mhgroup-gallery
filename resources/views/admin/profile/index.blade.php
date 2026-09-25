@extends('layouts.admin.app')

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <div class="header-wrapper">
                <form action="{{ Route('admin.change-images') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="profile-header">
                        <div id="save-cover-image">
                            <div class="absolute-top-left z-3 w-100 d-flex align-items-center justify-content-between gap-3 p-md-3 p-10px"
                                style="background-color: rgb(0 0 0 / 40%);">
                                <div class="flex-grow-1">
                                    <p class="mb-0 text-white fw-500 fs-16 d-sm-block d-none">Do you want to save?</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm close-btn">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-sm" id="save-cover-btn">Save
                                            Change</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="absolute-top-right z-2 p-10px">
                            <input class="d-none" type="file" id="cover_image" name="cover_image"
                                accept=".jpg, .jpeg, .png, .webp">
                            <label for="cover_image" class="btn btn-sm btn-primary">Edit Cover Image</label>
                        </div>
                        <img class="lazyload img-fit absolute-full"
                            data-src="{{ asset(file_exists(Auth::user()->cover_image) ? Auth::user()->cover_image : 'backend/images/avatar/default/cover.png') }}"
                            alt="Cover Image">
                        <div id="onUploadCover">
                            <img class="lazyload img-fit absolute-full z-2" id="onUploadCoverImage" src=""
                                alt="Cover Image">
                        </div>
                    </div>

                    <div class="profile-meta w-fit mx-auto">
                        <img class="user-img img-fit lazyload"
                            data-src="{{ asset(file_exists(Auth::user()->image) ? Auth::user()->image : 'backend/images/avatar/default/profile.jpg') }}"
                            alt="vendor image">
                        <div class="thumb-edit">
                            <input type="file" id="profileImageInput" class="image-upload" name="profile_image"
                                accept=".jpg, .jpeg, .png, .webp">
                            <label for="profileImageInput"><i class="fal fa-pencil-alt"></i></label>
                        </div>
                    </div>

                    <div id="save-profile-image">
                        <div class="inner">
                            <img class="img-fit" id="profileImage" src="" width="150" height="150"
                                alt="Image">
                            <div class="d-flex align-items-center justify-content-center gap-3 p-md-3 p-10px">
                                <button type="button" class="btn btn-sm close-btn">Cancel</button>
                                <button type="submit" class="btn btn-primary btn-sm" id="save-profile-btn">Save
                                    Change</button>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <h5 class="h5 mb-1 pt-sm-3 pt-2 text-primary fw-700">{{ Auth::user()->name }}</h5>
                        <p class="text mb-0">{{ Auth::user()->email }}</p>
                    </div>
                </form>
            </div>

            <div>
                <nav class="admin-profile-tab nav__wrapper">
                    <div class="nav nav-tabs p-10px overflow-auto text-nowrap flex-nowrap" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-admin-info-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-admin-info" type="button" role="tab" aria-controls="nav-admin-info"
                            aria-selected="true">Admin
                            Info</button>
                        <button class="nav-link" id="nav-info-edit-tab" data-bs-toggle="tab" data-bs-target="#nav-info-edit"
                            type="button" role="tab" aria-controls="nav-info-edit" aria-selected="false">Profile
                            Settings</button>
                        <button class="nav-link" id="nav-password-edit-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-password-edit" type="button" role="tab"
                            aria-controls="nav-password-edit" aria-selected="false">Change
                            Password</button>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-admin-info" role="tabpanel"
                        aria-labelledby="nav-admin-info-tab">
                        <div class="bg-white rounded-3 p-sm-4 p-3">
                            <h5 class="h5 mb-3 fw-600 text-primary">Profile Information</h5>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Name
                                    <span class="rounded-pill">{{ Auth::user()->name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Email Address
                                    <span class="rounded-pill">{{ Auth::user()->email }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Phone
                                    <span class="rounded-pill">{{ Auth::user()->phone }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Address
                                    <span class="rounded-pill">{{ Auth::user()->address }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-info-edit" role="tabpanel" aria-labelledby="nav-info-edit-tab">
                        <div class="bg-white rounded-3 p-sm-4 p-3">
                            <h5 class="h5 mb-3 fw-600 text-primary">Profile Settings</h5>
                            <form action="{{ Route('admin.profile.update') }}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="row g-3">
                                    <div class="col-md-4 col-xs-6">
                                        <label for="admin-name" class="form-label">Your Name</label>
                                        <input class="form-control" type="text" id="admin-name"
                                            placeholder="Your Name" name="name" value="{{ Auth::user()->name }}">
                                    </div>
                                    <div class="col-md-4 col-xs-6">
                                        <label for="admin-email" class="form-label">Your Email</label>
                                        <input class="form-control" type="email" id="admin-email"
                                            placeholder="Your Email" name="email" value="{{ Auth::user()->email }}">
                                    </div>
                                    <div class="col-md-4 col-xs-6">
                                        <label for="admin-phone" class="form-label">Your Phone</label>
                                        <input class="form-control" type="number" id="admin-phone"
                                            placeholder="Your Phone" name="phone" value="{{ Auth::user()->phone }}">
                                    </div>
                                    <div class="col-12">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" cols="30" rows="5"
                                            placeholder="Write your address here...">{{ Auth::user()->address }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <div class="pt-3 text-center">
                                            <button type="submit"
                                                class="btn px-5 py-2 btn-primary btn-sm">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-password-edit" role="tabpanel"
                        aria-labelledby="nav-password-edit-tab">
                        <div class="bg-white rounded-3 p-sm-4 p-3">
                            <h5 class="h5 mb-3 fw-600 text-primary">Change Password</h5>
                            <form action="{{ Route('admin.change-password') }}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="row g-3">
                                    <div class="col-md-4 col-xs-6">
                                        <label for="old-password" class="form-label"><b>Old Password</b></label>
                                        <div class="input-group">
                                            <button type="button"
                                                class="input-group-text password-toggler text-primary order-last"><i
                                                    class="fas fa-eye-slash"></i></button>
                                            <input class="form-control order-first" type="password" id="old-password"
                                                placeholder="Old Password" required name="old_password">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xs-6">
                                        <label for="new-password" class="form-label"><b>New Password</b></label>
                                        <div class="input-group">
                                            <button type="button"
                                                class="input-group-text password-toggler text-primary order-last"><i
                                                    class="fas fa-eye-slash"></i></button>
                                            <input class="form-control order-first" type="password" id="new-password"
                                                placeholder="New Password" required name="new_password">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xs-6">
                                        <label for="confirm-password" class="form-label"><b>Confirm Password</b></label>
                                        <div class="input-group">
                                            <button type="button"
                                                class="input-group-text password-toggler text-primary order-last"><i
                                                    class="fas fa-eye-slash"></i></button>
                                            <input class="form-control order-first" type="password" id="confirm-password"
                                                placeholder="Confirm Password" required name="new_password_confirmation">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="pt-3 text-center">
                                            <button type="submit"
                                                class="btn px-5 py-2 btn-primary btn-sm">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {

            cover_image.onchange = evt => {
                const [file] = cover_image.files
                if (file) {
                    onUploadCoverImage.src = URL.createObjectURL(file);
                    $('#onUploadCover').addClass('d-block');
                    $('#save-cover-image').addClass('d-block');
                }
            }

            profileImageInput.onchange = evt => {
                const [file] = profileImageInput.files
                if (file) {
                    profileImage.src = URL.createObjectURL(file);
                    $('#save-profile-image').addClass('d-grid');
                }
            }

            $('.close-btn').on('click', function() {
                $('#save-profile-image').removeClass('d-grid');
                $('#profileImageInput').val('');

                $('#onUploadCover').removeClass('d-block');
                $('#save-cover-image').removeClass('d-block');
                $('#cover_image').val('');
            });

            $('#profileImageInput').on('change', function() {
                $('#onUploadCover').removeClass('d-block');
                $('#save-cover-image').removeClass('d-block');
                $('#cover_image').val('');
            });
        });
    </script>
@endpush
