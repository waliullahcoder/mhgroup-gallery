@extends('layouts.admin.index_app')

@section('content')
@php
    $currentRouteName = \Request::route()->getName();
    $ajaxUrl = route($currentRouteName);
    $deletePermission = str_replace('index', 'destroy', $currentRouteName);
    $deleteUrl = route($deletePermission, 0);
@endphp

<div class="card">
    <div class="card-body">
        <table class="dataTable table table-bordered table-hover align-middle w-100">
            <thead>
                <tr class="text-nowrap align-middle">
                    <th></th>
                    <th>Thumbnail</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Edition</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>

            @can($deletePermission)
            <tfoot>
                <tr>
                    <th class="text-center">
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th colspan="7">
                        <div class="text-end">
                            <button type="button" id="bulk_delete" data-url="{{ $deleteUrl }}"
                                class="btn btn-xs btn-danger">Delete</button>
                        </div>
                    </th>
                </tr>
            </tfoot>
            @endcan
        </table>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {

    var table = $('.dataTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ $ajaxUrl }}",
            type: "GET",
            data: function(d) {
                d.type = $('#filter').val();
            }
        },
        columns: [
            { data: "checkbox", name: "checkbox", orderable: false, searchable: false, className: "text-center", width: '30px' },
            { data: 'thumbnail', name: 'thumbnail', orderable: false, searchable: false, width: '70px' },
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            // { data: 'categories.name', name: 'categories.name', defaultContent: '', width: '150px' },
           { data: 'category_names', name: 'category_names', orderable: false, searchable: true, width: '150px' },
            { data: 'edition.name', name: 'edition.name', defaultContent: '', width: '100px' },
            { data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center', width: '90px' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end', width: '120px' }
        ],
        fnDrawCallback: function() {
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Status toggle ajax
            $('.status-toggle').off('change').on('change', function(){
                let id = $(this).data('id');
                let status = $(this).is(':checked') ? 'active' : 'inactive';

                $.ajax({
                    url: route("admin.product.edit",d.id),
                    method: 'get',
                    data: { id: id, status: status, _token: '{{ csrf_token() }}' },
                    success: function(res){
                        console.log(res);
                    }
                });
            });
        }
    });

    // Select All checkbox
    $(document).on('change', '#selectAll', function() {
        $('input[type="checkbox"]').prop('checked', $(this).prop('checked'));
    });

});
</script>
@endpush