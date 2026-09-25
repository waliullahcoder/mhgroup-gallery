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
                <th>Date</th>
                <th>Serial No</th>
                <th>Year</th>
                <th>Month</th>
                <th>Invest Qty</th>
                <th>Invest Amount</th>
                <th>Profit Amount</th>
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
                    <th colspan="8">
                        <div class="text-end">
                            <button type="button" id="bulk_delete" name="bulk_delete" data-url="{{ $deleteUrl }}"
                                class="btn btn-xs btn-danger">
                                Delete
                            </button>
                            <button type="button" id="trash_bulk_delete" name="bulk_delete" data-url="{{ $deleteUrl }}"
                                class="btn btn-xs btn-danger" style="display: none;">
                                Delete
                            </button>
                        </div>
                    </th>
                </tr>
            </tfoot>
        @endcan
    </table>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('.dataTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ $ajaxUrl }}",
                    type: "GET",
                    data: function(data) {
                        data.type = $('#filter').val();
                    },
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
                        data: 'formattedDate',
                        name: 'formattedDate',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'serial_no',
                        name: 'serial_no'
                    },
                    {
                        data: 'year',
                        name: 'year',
                    },
                    {
                        data: 'month',
                        name: 'month',
                    },
                    {
                        data: 'invest_qty',
                        name: 'invest_qty',
                        className: "text-end"
                    },
                    {
                        data: 'invest_amount',
                        name: 'invest_amount',
                        className: "text-end"
                    },
                    {
                        data: 'profit_amount',
                        name: 'profit_amount',
                        className: "text-end"
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: "text-end",
                        width: '100'
                    },
                ],
                fnDrawCallback: function() {
                    const tooltips = document.querySelectorAll('.tt');
                    tooltips.forEach(t => {
                        new bootstrap.Tooltip(t);
                    });
                }
            });
        });
    </script>
@endpush
