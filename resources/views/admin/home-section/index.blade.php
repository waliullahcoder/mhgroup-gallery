@extends('layouts.admin.index_app')

@section('content')
    @php
        $currentRouteName = \Request::route()->getName();
        $editPermission = str_replace('index', 'edit', $currentRouteName);
        $deletePermission = str_replace('index', 'destroy', $currentRouteName);
    @endphp
    <div class="menu-builder">
        <div class="dd nestable" id="nestable">
            <ol class="dd-list">
                @foreach ($homeSections as $item)
                    <li class="dd-item" data-id="{{ $item->id }}" id>
                        <div class="dd-handle">{{ $item->title }} - {{ $item->type }}</div>
                        <div class="btn-group">
                            @can($editPermission)
                                <a href="{{ Route($editPermission, $item->id) }}" class="btn btn-sm btn-warning" title=""
                                    data-bs-original-title="Edit" aria-label="Edit"><i class="far fa-pencil-alt"></i></a>
                            @endcan
                            @can($deletePermission)
                                <button type="button" class="btn btn-sm btn-danger delete-item" id="button_{{ $item->id }}"
                                    data-id="{{ $item->id }}" data-url="{{ Route($deletePermission, $item->id) }}">
                                    <i class="far fa-trash-alt"></i></button>
                            @endcan
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript" src="{{ asset('backend/js/nestable.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.nestable').nestable({
                maxDepth: 1
            }).on('change', function(e) {
                let updatedOrder = $(this).nestable('serialize');

                $.ajax({
                    url: '{{ request()->fullUrl() }}',
                    method: 'POST',
                    data: {
                        _method: 'GET',
                        order: updatedOrder
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            Swal.fire({
                                width: "22rem",
                                position: 'top-right',
                                toast: true,
                                text: 'Serial Updated!',
                                icon: 'success',
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
                    },
                    error: function(xhr) {
                        console.error('Error updating order:', xhr.responseText);
                    }
                });
            });

            function showToast(title, text, icon = 'success') {
                Swal.fire({
                    width: "22rem",
                    position: 'top-right',
                    toast: true,
                    text,
                    icon,
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

            $(document).on('click', '.delete-item', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                parmanent: undefined
                            },
                            success: res => {
                                $('#button_' + id).closest('li').remove();
                                showToast(res.status === 'success' ? 'Deleted!' :
                                    'Failed!', res.message || '', res.status);

                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
