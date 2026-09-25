@extends('layouts.admin.report_app')

@section('form')
    <div class="row g-3">
        <input type="hidden" name="print" value="">
        <input type="hidden" name="filter" value="1">
        @if (Auth::user()->hasRole('Investor'))
            <input type="hidden" id="investor_id" name="investor_id" value="{{ Auth::user()->investor->id }}">
        @else
            <div class="col-sm-6">
                <label for="investor_id" class="form-label"><b>Investor <span class="text-danger">*</span></b></label>
                <select name="investor_id" id="investor_id" class="form-select select" data-placeholder="Select Investor"
                    required>
                    <option value=""></option>
                    @php
                        $investors = \App\Models\Investor::orderBy('id', 'desc')->get();
                    @endphp
                    @foreach ($investors as $item)
                        <option value="{{ $item->id }}" {{ request('investor_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="{{ Auth::user()->hasRole('Investor') ? 'col-12' : 'col-sm-6' }}">
            <label for="date_range" class="form-label"><b>Date <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control date-range" name="date_range" id="date_range"
                placeholder="Select Date Range" data-time-picker="true" data-format="DD-MM-Y" data-separator=" to "
                autocomplete="off"
                value="{{ date('d-m-Y', strtotime($start_date)) . ' to ' . date('d-m-Y', strtotime($end_date)) }}" required>
        </div>
    </div>
@endsection

@section('content')
    <table class="dataTable table table-bordered w-100">
        <thead>
            <tr>
                <th class="text-end" colspan="5">Previous Balance</th>
                <th class="text-end">{{ number_format($previous_balance, 2) }}</th>
            </tr>
            <tr>
                <th class="text-center" width="40px">Sl#</th>
                <th>Date</th>
                <th>Remarks</th>
                <th class="text-end">Amount In</th>
                <th class="text-end">Amount Out</th>
                <th class="text-end">Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $balance = $previous_balance;
                $amount_in = 0;
                $amount_out = 0;
            @endphp
            @foreach ($statements as $row)
                @php
                    $balance += $row['amount_in'] - $row['amount_out'];
                    $amount_in += $row['amount_in'];
                    $amount_out += $row['amount_out'];
                @endphp
                <tr class="text-nowrap">
                    <td class="text-center px-3">{{ $loop->iteration }}</td>
                    <td>{{ date('d-m-Y', strtotime($row['date'])) }}</td>
                    <td>{{ $row['remarks'] }}</td>
                    <td class="text-end">{{ number_format($row['amount_in'], 2) }}</td>
                    <td class="text-end">{{ number_format($row['amount_out'], 2) }}</td>
                    <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.dataTable').DataTable({
                order: false,
                responsive: true,
                dom: "<'row g-2'<'col-sm-4'l><'col-sm-8 text-end'<'d-lg-flex justify-content-end'<'mb-2 mb-lg-0 me-1'f>B>>>t<'d-lg-flex align-items-center mt-2'<'me-auto mb-lg-0 mb-2'i><'mb-0'p>>",
                lengthMenu: [10, 20, 30, 40, 50],
                buttons: [{
                        extend: 'excel',
                        text: '<i class="fal fa-file-spreadsheet"></i> Excel'
                    },
                    {
                        text: '<i class="fal fa-file-pdf"></i> Print',
                        className: 'getPdf',
                        action: function(e, dt, node, config) {
                            var investor_id = $('#investor_id').val();
                            if (investor_id == null || investor_id == '') {
                                Swal.fire({
                                    width: "22rem",
                                    toast: true,
                                    position: 'top-right',
                                    text: 'Please select a investor!',
                                    icon: "error",
                                    showConfirmButton: false,
                                    timer: 1500,
                                    showClass: {
                                        popup: `animate__animated  animate__bounceInRight  animate__faster`
                                    },
                                    hideClass: {
                                        popup: `animate__animated animate__bounceOutRight animate__faster`
                                    }
                                });
                                return false;
                            }
                            $('input[name="print"]').val('true');
                            $('.filter_form')[0].setAttribute("target", "_blank");
                            $('.filter_form')[0].submit();
                        }
                    }
                ]
            });

            $(document).on('click', '#filter_btn', function(e) {
                $('input[name="print"]').val('');
                $('.filter_form')[0].setAttribute("target", "_self");
            });
        });
    </script>
@endpush
