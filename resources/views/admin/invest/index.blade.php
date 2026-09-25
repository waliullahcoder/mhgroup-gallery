@extends('layouts.admin.index_app')

@section('content')
    <table class="dataTable table align-middle" style="width:100%">
        <thead>
            <tr class="text-nowrap">
                <th>SL#</th>
                <th>Date</th>
                <th>Invest No</th>
                <th>Book Name</th>
                <th>Investor Name</th>
                <th>Phone</th>
                <th>Qty</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr class="bg-primary">
                <th class="text-white" colspan="6">
                    <div class="text-end">Total</div>
                </th>
                <th class="text-white" id="totalQty"></th>
                <th class="text-white" id="totalAmount"></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('.dataTable').DataTable({
                dom: "<'row g-2'<'col-sm-4'l><'col-sm-8 text-end'fB>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>",
                lengthMenu: [10, 20, 30, 40, 50],
                buttons: [],
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ request()->fullUrl() }}",
                    type: "GET",
                    data: function(data) {
                        data.type = $('#filter').val();
                        data.customFilter = $('#customFilter').val(); // custom filter value
                    },
                    dataSrc: function(json) {
                        $('#totalQty').html(json.totalQty ?? 0);
                        $('#totalAmount').html((json.totalAmount ?? 0).toLocaleString());
                        return json.data;
                    }
                },
                columns: [{
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
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
                        data: 'invest_no',
                        name: 'invest_no'
                    },
                    {
                        data: 'product.name',
                        name: 'product.name',
                        defaultContent: ''
                    },
                    {
                        data: 'investor.name',
                        name: 'investor.name',
                        defaultContent: ''
                    },
                    {
                        data: 'investor.phone',
                        name: 'investor.phone',
                        defaultContent: ''
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                        className: "text-end"
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        className: "text-end"
                    },
                    {
                        data: null,
                        name: 'sattled',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            if(row.sattled == 1){
                                return '<span class="btn btn-xs btn-danger">Sattled</span>';
                            } else {
                                return '<span class="btn btn-xs btn-success text-nowrap">On Going</span>';
                            }
                        },
                        className: "text-end",
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
                },
            });

            // ✅ Append the custom selector after the search box
            $('.dataTables_filter').append(`
                <select id="customFilter" class="form-select form-select-sm fs-12" style="width:80px;min-height: 27px;display:inline-block;">
                    <option value="">All</option>
                    <option value="Closed">Closed</option>
                    <option value="On Going">On Going</option>
                </select>
            `);

            // ✅ Reload table when selector changes
            $(document).on('change', '#customFilter', function() {
                table.draw();
            });
        });
    </script>
@endpush
