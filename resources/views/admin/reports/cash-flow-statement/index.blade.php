@extends('layouts.admin.report_app')

@section('form')
    <input type="hidden" name="print" value="">
    <input type="hidden" name="filter" value="1">
    <div class="row g-3">
        <div class="col-12">
            <label for="date_range" class="form-label"><b>Date</b></label>
            <input type="text" class="form-control date-range" name="date_range" id="date_range"
                placeholder="Select Date Range" data-time-picker="true" data-format="DD-MM-Y" data-separator=" to "
                autocomplete="off" required
                value="{{ date('d-m-Y', strtotime($start_date)) . ' to ' . date('d-m-Y', strtotime($end_date)) }}">
        </div>
    </div>
@endsection

@section('content')
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th class="text-center" width="20">SL#</th>
                    <th>Date</th>
                    <th class="text-end">Cash Opening</th>
                    <th class="text-end">Cash Receive</th>
                    <th class="text-end">Cash Payment</th>
                    <th class="text-end">Cash Balance</th>
                    <th class="text-end">Bank Opening</th>
                    <th class="text-end">Bank Receive</th>
                    <th class="text-end">Bank Payment</th>
                    <th class="text-end">Bank Balance</th>
                    <th class="text-end">Total Balance</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_cash_receive = 0;
                    $total_cash_payment = 0;
                    $total_bank_receive = 0;
                    $total_bank_payment = 0;
                @endphp
                @foreach ($data as $row)
                    @php
                        $lastDate = Date('Y-m-d', strtotime('-1 day', strtotime($row->date)));
                        // Cash
                        $openRecCashAmount = \App\Models\AccountTransaction::where('date', '<=', $lastDate)
                            ->whereIn('voucher_type', ['CV', 'JV'])
                            ->where('coa_head_code', 'like', $generalLedgerHeadCash . '%')
                            ->sum('debit_amount');
                        $openPayCashAmount = \App\Models\AccountTransaction::where('date', '<=', $lastDate)
                            ->whereIn('voucher_type', ['DV', 'JV'])
                            ->where('coa_head_code', 'like', $generalLedgerHeadCash . '%')
                            ->sum('credit_amount');
                        $recCashAmount = \App\Models\AccountTransaction::where('date', $row->date)
                            ->whereIn('voucher_type', ['CV', 'JV'])
                            ->where('coa_head_code', 'like', $generalLedgerHeadCash . '%')
                            ->sum('debit_amount');
                        $payCashAmount = \App\Models\AccountTransaction::where('date', $row->date)
                            ->whereIn('voucher_type', ['DV', 'JV'])
                            ->where('coa_head_code', 'like', $generalLedgerHeadCash . '%')
                            ->sum('credit_amount');
                        // Bank
                        $openRecBankAmount = \App\Models\AccountTransaction::where('date', '<=', $lastDate)
                            ->whereIn('voucher_type', ['CV', 'JV'])
                            ->where('coa_head_code', 'like', $generalLedgerHeadBank . '%')
                            ->sum('debit_amount');
                        $openPayBankAmount = \App\Models\AccountTransaction::where('date', '<=', $lastDate)
                            ->whereIn('voucher_type', ['DV', 'JV'])
                            ->where('coa_head_code', 'like', $generalLedgerHeadBank . '%')
                            ->sum('credit_amount');
                        $recBankAmount = \App\Models\AccountTransaction::where('date', $row->date)
                            ->whereIn('voucher_type', ['CV', 'JV'])
                            ->where('coa_head_code', 'like', $generalLedgerHeadBank . '%')
                            ->sum('debit_amount');
                        $payBankAmount = \App\Models\AccountTransaction::where('date', $row->date)
                            ->whereIn('voucher_type', ['DV', 'JV'])
                            ->where('coa_head_code', 'like', $generalLedgerHeadBank . '%')
                            ->sum('credit_amount');

                        $total_cash_receive += $recCashAmount;
                        $total_cash_payment += $payCashAmount;
                        $total_bank_receive += $recBankAmount;
                        $total_bank_payment += $payBankAmount;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ date('d-m-Y', strtotime($row->date)) }}</td>
                        <td class="text-end">{{ number_format($openRecCashAmount - $openPayCashAmount, 2) }}</td>
                        <td class="text-end">{{ number_format($recCashAmount, 2) }}</td>
                        <td class="text-end">{{ number_format($payCashAmount, 2) }}</td>
                        <td class="text-end">
                            {{ number_format($openRecCashAmount - $openPayCashAmount + $recCashAmount - $payCashAmount, 2) }}
                        </td>
                        <td class="text-end">{{ number_format($openRecBankAmount - $openPayBankAmount, 2) }}</td>
                        <td class="text-end">{{ number_format($recBankAmount, 2) }}</td>
                        <td class="text-end">{{ number_format($payBankAmount, 2) }}</td>
                        <td class="text-end">
                            {{ number_format($openRecBankAmount - $openPayBankAmount + $recBankAmount - $payBankAmount, 2) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($openRecCashAmount - $openPayCashAmount + $recCashAmount - $payCashAmount + ($openRecBankAmount - $openPayBankAmount + $recBankAmount - $payBankAmount), 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <th class="text-end" colspan="2">Total Summary</th>
                <th></th>
                <th class="text-end">{{ number_format($total_cash_receive, 2) }}</th>
                <th class="text-end">{{ number_format($total_cash_payment, 2) }}</th>
                <th></th>
                <th></th>
                <th class="text-end">{{ number_format($total_bank_receive, 2) }}</th>
                <th class="text-end">{{ number_format($total_bank_payment, 2) }}</th>
                <th></th>
                <th></th>
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
                e.preventDefault();
                $('input[name="print"]').val('true');
                $('.filter_form')[0].setAttribute("target", "_blank");
                $('.filter_form').submit();
            });

            $(document).on('click', '#filter_btn', function(e) {
                $('input[name="print"]').val('');
                $('.filter_form')[0].setAttribute("target", "_self");
            });
        });
    </script>
@endpush
