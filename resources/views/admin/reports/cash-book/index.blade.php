@extends('layouts.admin.report_app')

@section('form')
    <input type="hidden" name="print" value="">
    <input type="hidden" name="filter" value="1">
    <div class="row g-3">
        <div class="col-sm-6">
            <label for="coa_id" class="form-label"><b>Voucher Account <span class="text-danger">*</span></b></label>
            <select name="coa_id" id="coa_id" class="form-select select" data-placeholder="Select Account" required>
                <option value=""></option>
                @foreach ($coas as $item)
                    <option value="{{ $item->id }}" {{ request('coa_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->head_name }} - {{ $item->head_code }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6">
            <label for="date_range" class="form-label"><b>Date <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control date-range" name="date_range" id="date_range"
                placeholder="Select Date Range" data-time-picker="true" data-format="DD-MM-Y" data-separator=" to "
                autocomplete="off"
                value="{{ date('d-m-Y', strtotime($start_date)) . ' to ' . date('d-m-Y', strtotime($end_date)) }}" required>
        </div>
    </div>
@endsection

@section('content')
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th class="text-end" colspan="6">Previous Balance</th>
                    <th class="text-end">{{ number_format($previousBalance, 2) }}</th>
                </tr>
                <tr>
                    <th class="text-center " width="40px">SL#</th>
                    <th>Date</th>
                    <th>Voucher No</th>
                    <th>Particular</th>
                    <th class="text-end">Debit</th>
                    <th class="text-end">Credit</th>
                    <th class="text-end">Balance</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $balance = $previousBalance;
                    $totalDebit = 0;
                    $totalCredit = 0;
                @endphp
                @foreach ($data as $row)
                    @php
                        $balance += $row->debit_amount - $row->credit_amount;
                        $totalDebit += $row->debit_amount;
                        $totalCredit += $row->credit_amount;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ date('d-m-Y', strtotime($row->date)) }}</td>
                        <td>{{ $row->voucher_no }}</td>
                        <td>{{ $row->narration }}</td>
                        <td class="text-end">{{ number_format($row->debit_amount, 2) }}</td>
                        <td class="text-end">{{ number_format($row->credit_amount, 2) }}</td>
                        <td class="text-end">
                            {{ $balance >= 0 ? number_format($balance, 2) : '(' . number_format(abs($balance), 2) . ')' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-primary">
                    <th colspan="4" class="text-white text-end ">Total Summary</th>
                    <th class="text-white text-end ">
                        {{ number_format($totalDebit, 2) }}
                    </th>
                    <th class="text-white text-end ">
                        {{ number_format($totalCredit, 2) }}
                    </th>
                    <th class="text-white text-end ">
                        {{ $balance >= 0 ? number_format($balance, 2) : '(' . number_format(abs($balance), 2) . ')' }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "order": false,
                dom: 'lBfrtip',
                buttons: [
                    'excelHtml5',
                    {
                        'text': '<i class="fal fa-file-pdf"></i> Print',
                        'className': 'getPdf',
                    },
                ]
            });

            $(document).on('click', '.getPdf', function(e) {
                if ($('#coa_id').val() == '') {
                    Swal.fire({
                        width: "22rem",
                        toast: true,
                        position: 'top-right',
                        text: "Select an Account!",
                        icon: "error",
                        showConfirmButton: false,
                        timer: 1500,
                        showClass: {
                            popup: `
                                animate__animated
                                animate__bounceInRight
                                animate__faster
                            `
                        },
                        hideClass: {
                            popup: `
                                animate__animated
                                animate__bounceOutRight
                                animate__faster
                            `
                        }
                    });
                    return false;
                }
                $('input[name="print"]').val('true');
                $('.filter_form')[0].setAttribute("target", "_blank");
                $('.filter_form')[0].submit();
            });

            $(document).on('click', '#filter_btn', function(e) {
                $('input[name="print"]').val('');
                $('.filter_form')[0].setAttribute("target", "_self");
            });
        });
    </script>
@endpush
