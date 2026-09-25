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
                <th>PO No.</th>
                <th>Order Date</th>
                <th>Supplier</th>
                <th>Total Amount</th>
                <th>Discount</th>
                <th>Tax</th>
                <th>Grand Total</th>
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
                    <th colspan="11">
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
    var table = $('.dataTable').dataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ $ajaxUrl }}",
            type: "GET"
        },
        columns: [
            { data: "checkbox", name: "checkbox", orderable: false, searchable: false, className: "text-center", width: '20' },
            { data: 'po_number', name: 'po_number' },
            { data: 'order_date', name: 'order_date', orderable: false, searchable: false },
            { data: 'vendor.name', name: 'vendor.name', defaultContent: '' },
            { data: 'total_amount', name: 'total_amount' },
            { data: 'discount_amount', name: 'discount_amount' },
            { data: 'tax_amount', name: 'tax_amount' },
            { data: 'grand_total', name: 'grand_total' },
            { 
                data: 'actions', 
                name: 'actions', 
                orderable: false, 
                searchable: false, 
                className: "text-end",
                render: function(data, type, row) {
                    let showUrl = "{{ route('admin.purchase-order.show', ':id') }}";
                    showUrl = showUrl.replace(':id', row.id);

                    let buttons = `
                        <a href="${showUrl}" class="btn btn-sm btn-info" title="Show">
                            <i class="fas fa-eye"></i>
                        </a>
                    `;

                    @can($deletePermission)
                        buttons += `
                            <button type="button" data-id="${row.id}" class="btn btn-sm btn-danger delete-btn">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    @endcan

                    return buttons;
                }
            }
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