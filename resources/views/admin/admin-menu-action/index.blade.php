@extends('layouts.admin.app')

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-row justify-content-between align-items-center pe-2 py-2">
                    <h6 class="h6 mb-0 text-uppercase">Admin Menu Actions Setup</h6>
                    <div>
                        <a href="{{ Route('admin.admin-menu.index') }}" class="btn btn-primary btn-sm">Go
                            Back</a>
                        @can('admin.admin-menu-action.create')
                            <a href="{{ Route('admin.admin-menu-action.create', request()->route('id')) }}"
                                class="btn btn-primary btn-sm">Add
                                New</a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <table class="dataTable table align-middle" style="width:100%">
                        <thead>
                            <tr class="text-nowrap">
                                <th>SL#</th>
                                <th>Name</th>
                                <th>Parent Menu</th>
                                <th>Route</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('.dataTable').dataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ request()->fullUrl() }}',
                    type: "GET",
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                        width: '30',
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'menu.name',
                        name: 'menu.name',
                        defaultContent: ''
                    },
                    {
                        data: 'route',
                        name: 'route'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: "text-end",
                        width: '90',
                    },
                ],
                "fnDrawCallback": function(oSettings) {
                    const tooltips = document.querySelectorAll('.tt');
                    tooltips.forEach(t => {
                        new bootstrap.Tooltip(t);
                    });
                }
            });
        });
    </script>
@endpush
