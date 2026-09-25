@extends('layouts.admin.index_app')

@section('content')
    @php
        $currentRouteName = \Request::route()->getName();
        $ajaxUrl = route($currentRouteName);
        $deletePermission = str_replace('index', 'destroy', $currentRouteName);
        $deleteUrl = route($deletePermission, 0);
    @endphp
    <table class="dataTable table align-middle" style="width:100%">
        <thead>
            <tr class="text-nowrap">
                <th></th>
                <th>Invoice</th>
                <th>Date</th>
                <th>Sales Type</th>
                <th>Client</th>
                <th>Amount</th>
                <th>TSO</th>
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
                    <th class="text-end" colspan="7">
                        <button type="button" name="bulk_delete" data-url="{{ $deleteUrl }}" id="bulk_delete"
                            class="btn btn btn-xs btn-danger">Delete</button>
                        <button type="button" name="bulk_delete" data-url="{{ $deleteUrl }}" style="display: none;"
                            id="trash_bulk_delete" class="btn btn btn-xs btn-danger">Delete</button>
                    </th>
                </tr>
            </tfoot>
        @endcan
    </table>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('.dataTable').dataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ $ajaxUrl }}",
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
                        className: 'text-center',
                        width: '30',
                    },
                    {
                        data: 'invoice',
                        name: 'invoice',
                    },
                    {
                        data: 'formattedDate',
                        name: 'formattedDate',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'sale_type',
                        name: 'sale_type',
                    },
                    {
                        data: 'client.name',
                        name: 'client.name',
                        defaultContent: ''
                    },
                    {
                        data: 'net_amount',
                        name: 'net_amount'
                    },
                    {
                        data: 'tso.name',
                        name: 'tso.name',
                        defaultContent: ''
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
