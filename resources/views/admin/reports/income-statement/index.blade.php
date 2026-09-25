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
    <div class="row g-4">
        <div class="col-md-6">
            <div class="row g-4">
                <div class="col-12">
                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr class="bg-primary text-white">
                                <th colspan="4" class="text-center">Income</th>
                            </tr>
                            <tr>
                                <th class="text-center" width="20">SL#</th>
                                <th>Head Code</th>
                                <th>Head Name</th>
                                <th class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalIncome = 0;
                            @endphp
                            @foreach ($incomes as $incomeList)
                                @php
                                    $totalIncome += abs($incomeList->debit_amount - $incomeList->credit_amount);
                                @endphp
                                <tr>
                                    <th class="text-center">{{ $loop->iteration }}</th>
                                    <td>
                                        <a href="{{ Route('admin.head-details.index') }}?coa_id={{ $incomeList->coa_id }}&start_date={{ $start_date }}&end_date={{ $end_date }}&income_statement=1"
                                            target="_blank">
                                            {{ $incomeList->coa->head_code }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ Route('admin.head-details.index') }}?coa_id={{ $incomeList->coa_id }}&start_date={{ $start_date }}&end_date={{ $end_date }}&income_statement=1"
                                            target="_blank">
                                            {{ $incomeList->coa->head_name }}
                                        </a>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ Route('admin.head-details.index') }}?coa_id={{ $incomeList->coa_id }}&start_date={{ $start_date }}&end_date={{ $end_date }}&income_statement=1"
                                            target="_blank">
                                            {{ number_format(abs($incomeList->debit_amount - $incomeList->credit_amount), 2) }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total</th>
                                <th class="text-end">{{ number_format($totalIncome, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <table class="table table-bordered table-sm mb-0">
                <thead>
                    <tr class="bg-primary text-white">
                        <th colspan="4" class="text-center">Expense</th>
                    </tr>
                    <tr>
                        <th class="text-center" width="60">SL#</th>
                        <th>Head Code</th>
                        <th>Head Name</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalExpanse = 0;
                    @endphp
                    @foreach ($expenses as $expense)
                        @php
                            $totalExpanse += $expense->amount;
                        @endphp
                        <tr>
                            <th class="text-center">{{ $loop->iteration + 1 }}</th>
                            <td>
                                <a href="{{ Route('admin.head-details.index') }}?coa_id={{ $expense->coa_id }}&start_date={{ $start_date }}&end_date={{ $end_date }}&income_statement=1"
                                    target="_blank">
                                    {{ $expense->coa->head_code }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ Route('admin.head-details.index') }}?coa_id={{ $expense->coa_id }}&start_date={{ $start_date }}&end_date={{ $end_date }}&income_statement=1"
                                    target="_blank">
                                    {{ $expense->coa->head_name }}
                                </a>
                            </td>
                            <td class="text-end">
                                <a href="{{ Route('admin.head-details.index') }}?coa_id={{ $expense->coa_id }}&start_date={{ $start_date }}&end_date={{ $end_date }}&income_statement=1"
                                    target="_blank">
                                    {{ $expense->amount >= 0 ? number_format($expense->amount, 2) : '(' . number_format(abs($expense->amount), 2) . ')' }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th class="text-end">
                            {{ $totalExpanse >= 0 ? number_format($totalExpanse, 2) : '(' . number_format(abs($totalExpanse), 2) . ')' }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="col-12">
            @php
                $netIncome = $totalIncome;
            @endphp

            @if ($netIncome > $totalExpanse)
                <h5 class="text-white bg-success text-center mb-0 py-2">Net Profit:
                    {{ number_format($netIncome - $totalExpanse, 2) }}
                </h5>
            @endif

            @if ($netIncome < $totalExpanse)
                <h5 class="text-white bg-danger text-center mb-0 py-2">Net Lose:
                    {{ number_format($totalExpanse - $netIncome, 2) }}
                </h5>
            @endif
        </div>
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
                $('.filter_form').submit();
            });

            $(document).on('click', '#filter_btn', function(e) {
                e.preventDefault();
                $('input[name="print"]').val('');
                $('.filter_form').submit();
            });
        });
    </script>
@endpush
