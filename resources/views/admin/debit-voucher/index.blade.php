@extends('layouts.admin.index_app')

@section('content')
    <table class="dataTable table align-middle" style="width:100%">
        <thead>
            <tr class="text-nowrap">
                <th>SL#</th>
                <th>Date</th>
                <th>Voucher No</th>
                <th>Debit Head</th>
                <th>Credit Head</th>
                <th>Amount</th>
                <th>Account Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
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
                    url: "{{ request()->fullUrl() }}",
                    type: "GET",
                    data: function(data) {
                        data.type = $('#filter').val();
                    }
                },
                columns: [{
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                        width: '20'
                    },
                    {
                        data: 'formattedDate',
                        name: 'formattedDate',
                        orderable: false,
                        searchable: false,
                        className: "text-nowrap",
                    },
                    {
                        data: 'voucher_no',
                        name: 'voucher_no',
                    },
                    {
                        data: 'debit_head',
                        name: 'debit_head',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'coa.head_name',
                        name: 'coa.head_name',
                        defaultContent: ''
                    },
                    {
                        data: 'credit_amount',
                        name: 'credit_amount',
                    },
                    {
                        data: 'approved',
                        name: 'approved',
                        orderable: false,
                        searchable: false,
                        width: '120'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: "text-end",
                        width: '90'
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
