@extends('layouts.admin.app')

@section('content')
    @php
        $currentRouteName = \Request::route()->getName();
        $deletePermission = str_replace('index', 'destroy', $currentRouteName);
        $deleteUrl = route($deletePermission, 0);
    @endphp
    <div class="card">
        <div class="card-header pe-2 py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="h6 mb-0 text-uppercase py-5px">{{ @$title ?? 'Please Set Title' }}</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ Route('admin.attribute.index') }}" class="btn btn-primary btn-sm">Go
                        Back</a>
                    @if (Auth::user()->can('admin.attribute-value.create'))
                        <a href="{{ Route('admin.attribute-value.create', request()->segment(3)) }}"
                            class="btn btn-primary btn-sm" id="addBtn">Add
                            New</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="dataTable table align-middle" style="width:100%">
                <thead>
                    <tr class="text-nowrap">
                        <th></th>
                        <th>Attribute</th>
                        <th>Value</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>

                @can($deletePermission)
                    <tfoot>
                        <tr>
                            <th class="text-center">
                                <div class="custom-control custom-checkbox mx-auto">
                                    <div id="regular_all_select">
                                        <input type="checkbox" class="custom-control-input" id="selectAll">
                                        <label class="custom-control-label" for="selectAll"></label>
                                    </div>
                                    <div id="trash_all_select" style="display: none;">
                                        <input type="checkbox" class="custom-control-input" id="trash_selectAll">
                                        <label class="custom-control-label" for="trash_selectAll"></label>
                                    </div>
                                </div>
                            </th>
                            <th colspan="4">
                                <div class="text-end">
                                    <button type="button" id="bulk_delete" name="bulk_delete" data-url="{{ $deleteUrl }}"
                                        class="btn btn-xs btn-danger">
                                        Delete
                                    </button>
                                    <button type="button" id="trash_bulk_delete" name="bulk_delete"
                                        data-url="{{ $deleteUrl }}" class="btn btn-xs btn-danger" style="display: none;">
                                        Delete
                                    </button>
                                </div>
                            </th>
                        </tr>
                    </tfoot>
                @endcan
            </table>
        </div>
    </div>

    @include('admin.attribute-value.partial.create')
    <div id="editForm"></div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('.dataTable').dataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ request()->fullUrl() }}",
                    type: "GET",
                    data: function(data) {
                        data.type = $('#filter').val();
                    }
                },
                columns: [{
                        data: "checkbox",
                        name: "checkbox",
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                        width: '20'
                    },
                    {
                        data: 'attribute.name',
                        name: 'attribute.name',
                        defaultContent: '',
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: "text-end",
                        width: '90'
                    }
                ],
                "fnDrawCallback": function(oSettings) {
                    const tooltips = document.querySelectorAll('.tt');
                    tooltips.forEach(t => {
                        new bootstrap.Tooltip(t);
                    });
                }
            });

            $(document).on('click', '#addBtn', function(e) {
                e.preventDefault();
                $('#addModal').modal('show');
            });

            $(document).on('click', '.link-edit', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                $('#editForm').html('');
                $.ajax({
                    url,
                    type: 'POST',
                    data: {
                        _method: 'GET',
                        edit: true,
                    },
                    success: res => {
                        $('#editForm').html(res.data);
                        $('#editModal').modal('show');
                    }
                });
            });

            $('#editModal').on('hidden.bs.modal', function() {
                $('#editForm').html('');
            });
        });
    </script>
@endpush
