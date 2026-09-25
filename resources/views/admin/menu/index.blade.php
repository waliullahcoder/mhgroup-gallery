@extends('layouts.admin.app')

@section('content')
    @php
        $currentRouteName = \Request::route()->getName();
        $ajaxUrl = route($currentRouteName);
        $deletePermission = str_replace('index', 'destroy', $currentRouteName);
        $deleteUrl = route($deletePermission, 0);
        $createRoute = str_replace('index', 'create', $currentRouteName);
        $trashRoute = str_contains($currentRouteName, '.index')
            ? str_replace('index', 'trash', $currentRouteName)
            : str_replace('pending', 'trash', $currentRouteName);
    @endphp
    <div class="row g-3">
        <div class="col-lg-4 order-lg-last">
            @if ($createRoute)
                <form action="{{ Route('admin.menu.store') }}" method="POST" enctype="multipart/form-data" id="addForm">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h6 class="h6 mb-0 py-5px">Add New Menu</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                     <label for="name" class="form-label"><b>Select Category <span
                                                class="text-danger">*</span></b></label>
                                <select id="category_id" class="form-control" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label"><b>Select Sub Category</b></label>

                                    <select id="sub_category_id" name="category_id" class="form-control">
                                        <option value="">-- Select Sub Category --</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="name" class="form-label"><b>Menu Name <span
                                                class="text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name') }}" placeholder="Menu Name" required>
                                </div>
                                 <div class="col-12">
                                    <label for="name" class="form-label"><b>Menu URL <span
                                                class="text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="url" name="url"
                                        value="{{ old('url') }}" placeholder="Menu url" required>
                                </div>
                                <div class="col-12">
                                    <label for="position" class="form-label"><b>Position</b></label>
                                    <select name="position" id="position" class="form-select" required>
                                      <option value="header_top" {{ old('position') == 'header_top' ? 'selected' : '' }}>
                                        Header Top
                                    </option>

                                    <option value="header" {{ old('position') == 'header' ? 'selected' : '' }}>
                                        Header Middle
                                    </option>

                                    <option value="mega_menu" {{ old('position') == 'mega_menu' ? 'selected' : '' }}>
                                        Mega Menu
                                    </option>

                                    <option value="footer" {{ old('position') == 'footer' ? 'selected' : '' }}>
                                        Footer Column1
                                    </option>

                                    <option value="footer_col2" {{ old('position') == 'footer_col2' ? 'selected' : '' }}>
                                        Footer Column2
                                    </option>

                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="status" class="form-label"><b>Status</b></label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Deactive
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end pe-2">
                            <button type="submit" class="btn btn-sm btn-primary">Save Now</button>
                        </div>
                    </div>
                </form>
            @endif
   
            <form action="" method="POST" id="editForm" enctype="multipart/form-data" style="display: none;">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-header">
                        <h6 class="h6 mb-0 py-5px">Edit Menu</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                     <label for="name" class="form-label"><b>Select Category <span
                                                class="text-danger">*</span></b></label>
                                     
                                <select name="category_id" class="form-control" required>
                                    @foreach($subbcategories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                </div>
                            <div class="col-12">
                                <label for="old_name" class="form-label"><b>Menu Name (EN) <span
                                            class="text-danger">*</span></b></label>
                                <input type="text" class="form-control" id="old_name" name="name"
                                    value="{{ old('name') }}" placeholder="Menu Name" required>
                            </div>
                            <div class="col-12">
                                <label for="old_url" class="form-label"><b>Menu URL (EN) <span
                                            class="text-danger">*</span></b></label>
                                <input type="text" class="form-control" id="old_url" name="url"
                                    value="{{ old('url') }}" placeholder="Menu URL" required>
                            </div>
                            <div class="col-12">
                                <label for="old_position" class="form-label"><b>Position</b></label>
                                <select name="position" id="old_position" class="form-select">
                                    <option value="header_top">Header Top</option>
                                    <option value="header">Header Middle</option>
                                    <option value="mega_menu">Mega Menu</option>
                                    <option value="footer">Footer Column1</option>
                                    <option value="footer_col2">Footer Column2</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="old_status" class="form-label"><b>Status</b></label>
                                <select name="status" id="old_status" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end pe-2">
                        <div class="d-flex justify-content-between gap-1">
                            <button type="button" class="btn btn-sm btn-warning" id="cancelBtn">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pe-2">
                    <h6 class="h6 mb-0 text-uppercase card-title">
                        {{ isset($title) ? $title : 'Please Set Title' }}</h6>
                    <div class="card-buttons">
                        @can($trashRoute)
                            <select name="filter" id="filter" class="form-select select-sm flex-shrink-0">
                                <option value="all">All</option>
                                <option value="trash">Trash</option>
                            </select>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <table class="dataTable table align-middle" style="width:100%">
                        <thead>
                            <tr class="text-nowrap">
                                <th></th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
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
                                            <button type="button" id="bulk_delete" name="bulk_delete"
                                                data-url="{{ $deleteUrl }}" class="btn btn-xs btn-danger">
                                                Delete
                                            </button>
                                            <button type="button" id="trash_bulk_delete" name="bulk_delete"
                                                data-url="{{ $deleteUrl }}" class="btn btn-xs btn-danger"
                                                style="display: none;">
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
        </div>
    </div>
@endsection

@push('js')
<script>
 const subCategories = @json($sub_categories);
    document.getElementById('category_id').addEventListener('change', function () {
        const categoryId = this.value;
        const subCategorySelect = document.getElementById('sub_category_id');

        // reset
        subCategorySelect.innerHTML = '<option value="">-- Select Sub Category --</option>';

        if (categoryId && subCategories[categoryId]) {
            subCategories[categoryId].forEach(function (sub) {
                const option = document.createElement('option');
                option.value = sub.id;
                option.textContent = sub.name;
                subCategorySelect.appendChild(option);
            });
        }
    });
</script>
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('.dataTable').dataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ Route('admin.menu.index') }}",
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
                        width: '30'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'position',
                        name: 'position'
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
                    },
                ],
                "fnDrawCallback": function(oSettings) {
                    const tooltips = document.querySelectorAll('.tt');
                    tooltips.forEach(t => {
                        new bootstrap.Tooltip(t);
                    });
                }
            });

            $(document).on('click', '.link-edit', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                $.ajax({
                    url: url,
                    data: {
                        _method: 'GET',
                        edit: true,
                    },
                    success: (response) => {
                        if (response.status == 'success') {
                            $('#editForm').attr('action', response.form_link);
                            $('#old_name').val(response.data.name);
                            $('#old_position').val(response.data.position);
                            $('#old_status').val(response.data.status);
                            $('#editForm').show();
                            $('#addForm').hide();
                        }
                        if (response.status == 'error') {
                            Swal.fire({
                                width: "22rem",
                                title: "Failed!",
                                text: "You don't have any Authority to do this action",
                                icon: "error",
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    }
                });
            });

            $('#cancelBtn').on('click', function() {
                $('#editForm').hide();
                $('#addForm').show();
            });
        });
    </script>
@endpush
