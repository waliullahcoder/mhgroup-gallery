@extends('layouts.admin.app')

@section('content')
    <div class="row g-3">
        <div class="col-lg-5 order-lg-last">
            <form action="{{ Route('admin.menu-item.update', $menu->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-header pe-2">
                        <h6 class="h6 mb-0 py-5px">Add Menu Item</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name" class="form-label"><b>Name <span
                                            class="text-danger">*</span></b></label>
                                <input type="text" class="form-control" name="name" id="name"
                                    value="{{ old('name') }}" autofocus required>
                            </div>
                            <div class="col-12">
                                <label for="type" class="form-label"><b>Type <span
                                            class="text-danger">*</span></b></label>
                                <select name="type" id="type" class="form-select" required>
                                    <option value="internal" {{ old('type') == 'internal' ? 'selected' : '' }}>Internal
                                    </option>
                                    <option value="external" {{ old('type') == 'external' ? 'selected' : '' }}>External
                                    </option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="link" class="form-label"><b>Link / Uri <span
                                            class="text-danger">*</span></b></label>
                                <input type="text" class="form-control" name="link" id="link"
                                    value="{{ old('link') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer pe-2 text-end">
                        <div class="d-flex align-items-center justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm">Save Now</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-7">
            <form action="{{ Route('admin.menu-item.store') }}" method="POST" id="serialize_form">
                @csrf
                <div class="card">
                    <div class="card-header pe-2">
                        <h6 class="h6 mb-0 text-uppercase card-title">{{ $menu->name }}</h6>
                        <div class="card-buttons">
                            <a href="{{ Route('admin.menu.index') }}" class="btn btn-primary btn-sm text-uppercase">Go
                                Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="menu-builder">
                            <div class="dd nestable" id="nestable">
                                <ol class="dd-list">
                                    @foreach ($menu->rootItems as $item)
                                        <li class="dd-item" data-id="{{ $item->id }}">
                                            <div class="dd-handle">{{ $item->name }}</div>
                                            <div class="btn-group">
                                                @if (count($item->children) === 0)
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger border-0 px-10px fs-15 link-delete"
                                                        data-url="{{ Route('admin.menu-item.destroy', $item->id) }}"><i
                                                            class="far fa-trash-alt"></i></button>
                                                @endif
                                            </div>
                                            @if (count($item->children) > 0)
                                                <ol class="dd-list">
                                                    @foreach ($item->children as $child)
                                                        <li class="dd-item" data-id="{{ $child->id }}">
                                                            <div class="dd-handle">{{ $child->name }}</div>
                                                            <div class="btn-group">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger border-0 px-10px fs-15 link-delete"
                                                                    data-url="{{ Route('admin.menu-item.destroy', $child->id) }}"><i
                                                                        class="far fa-trash-alt"></i></button>
                                                            </div>
                                                            @if (count($child->children) > 0)
                                                                <ol class="dd-list">
                                                                    @foreach ($child->children as $child)
                                                                        <li class="dd-item" data-id="{{ $child->id }}">
                                                                            <div class="dd-handle">{{ $child->name }}
                                                                            </div>
                                                                            <div class="btn-group">
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-danger border-0 px-10px fs-15 link-delete"
                                                                                    data-url="{{ Route('admin.menu-item.destroy', $child->id) }}"><i
                                                                                        class="far fa-trash-alt"></i></button>
                                                                            </div>
                                                                            @if (count($child->children) > 0)
                                                                                <ol class="dd-list">
                                                                                    @foreach ($child->children as $child)
                                                                                        <li class="dd-item"
                                                                                            data-id="{{ $child->id }}">
                                                                                            <div class="dd-handle">
                                                                                                {{ $child->name }}
                                                                                            </div>
                                                                                            <div class="btn-group">
                                                                                                <button type="button"
                                                                                                    class="btn btn-sm btn-danger border-0 px-10px fs-15 link-delete"
                                                                                                    data-url="{{ Route('admin.menu-item.destroy', $child->id) }}"><i
                                                                                                        class="far fa-trash-alt"></i></button>
                                                                                            </div>
                                                                                        </li>
                                                                                    @endforeach
                                                                                </ol>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ol>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            @endif
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer pe-2 text-end">
                        <button type="submit" class="btn btn-sm btn-primary">Update Order</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript" src="{{ asset('backend/js/nestable.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.nestable').nestable({
                maxDepth: {{ $menu->position == 'footer' ? 1 : 2 }}
            });

            $(document).on('submit', '#serialize_form', function(e) {
                e.preventDefault();
                let url = $(this).attr('action');
                let data = JSON.stringify($('.nestable').nestable('serialize'));
                $.ajax({
                    url: url,
                    method: "POST",
                    data: {
                        data: data,
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            Swal.fire({
                                toast: true,
                                width: "20rem",
                                icon: "success",
                                position: 'top-right',
                                text: 'Changed Successfull',
                                showConfirmButton: false,
                                timer: 1500,
                                showClass: {
                                    popup: `animate__animated animate__bounceInRight animate__faster`
                                },
                                hideClass: {
                                    popup: `animate__animated animate__bounceOutRight animate__faster`
                                }
                            });
                            location.reload();
                        }
                    }
                });
            });

            $(document).on('click', '.link-delete', function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                            },
                            success: function(response) {
                                if (response.status == 'success') {
                                    Swal.fire({
                                        width: "22rem",
                                        toast: true,
                                        position: 'top-right',
                                        text: response.status,
                                        icon: "success",
                                        showConfirmButton: false,
                                        timer: 1500,
                                        showClass: {
                                            popup: `animate__animated animate__bounceInRight animate__faster`
                                        },
                                        hideClass: {
                                            popup: `animate__animated animate__bounceOutRight animate__faster`
                                        }
                                    });
                                    location.reload();
                                }
                                if (response.status == 'error') {
                                    Swal.fire({
                                        width: "22rem",
                                        toast: true,
                                        position: 'top-right',
                                        text: "You don't have any Authority to do this action",
                                        icon: "error",
                                        showConfirmButton: false,
                                        timer: 1500,
                                        showClass: {
                                            popup: `animate__animated animate__bounceInRight animate__faster`
                                        },
                                        hideClass: {
                                            popup: `animate__animated animate__bounceOutRight animate__faster`
                                        }
                                    });
                                }
                            }
                        });
                    } else(
                        result.dismiss === Swal.DismissReason.cancel
                    )
                });
            });
        });
    </script>
@endpush
