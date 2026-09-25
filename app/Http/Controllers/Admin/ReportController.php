<?php

namespace App\Http\Controllers\Admin;

use App\Models\Coa;
use App\Models\Store;
use App\Models\Invest;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Investor;
use Carbon\CarbonPeriod;
use App\Models\TrialBalance;
use App\Models\Client;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\InvestSattlement;
use Illuminate\Support\Facades\DB;
use App\Models\AccountTransaction;
use App\Models\AccountTransactionAuto;
use App\DataTables\CoaListDataTable;
use App\DataTables\CollectionReportDataTable;
use App\DataTables\InvoiceWiseCollectionHistoryDataTable;
use App\DataTables\InvoiceWiseSalesHistoryDataTable;
use App\DataTables\InvoiceWiseSalesReturnHistoryDataTable;
use App\DataTables\ProductWiseSalesHistroyDataTable;
use App\DataTables\ProductWiseSalesReturnHistoryDataTable;
use App\DataTables\ProductWiseSalesReturnSummaryDataTable;
use App\DataTables\ProductWiseSalesSummaryDataTable;
use App\DataTables\SalesReportDataTable;
use App\DataTables\SalesReturnReportDataTable;
use App\DataTables\ClientWiseCollectionSummaryDataTable;

use App\Http\Controllers\Controller;
use App\Models\ProfitDistributionList;
use App\DataTables\voucherListDataTable;

class ReportController extends Controller
{
    public function coaList(Request $request, CoaListDataTable $dataTable)
    {
        if ($request->ajax() && $request->has('getHeads')) {
            $value = $request->head_type;
            if ($value == 'gl') {
                $heads = Coa::where('general', 1)->orderBy('head_name', 'asc')->get();
            } else {
                $heads = Coa::whereNull('parent_id')->orderBy('head_name', 'asc')->get();
            }
            return response()->json(['status' => 'success', 'heads' => $heads]);
        }

        if (!is_null($request->print)) {
            $query = Coa::query();
            $parent_head = $request->parent_head;
            if ($parent_head) {
                $query->where('head_code', 'LIKE', $parent_head . '%')->where('transaction', 1);
            }
            $data = $query->orderBy('head_name', 'asc')->get();

            $report_title = 'Chart of Accounts';
            $pdf = Pdf::loadView('admin.reports.coa-list.print', compact('report_title', 'data'));
            return $pdf->stream('coa_list_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Chart of Accounts';
        return $dataTable->render('admin.reports.coa-list.index', compact('title'));
    }

    public function voucherList(Request $request, voucherListDataTable $dataTable)
    {
        if ($request->ajax() && $request->has('getHeads')) {
            $value = $request->head_type;
            if ($value == 'gl') {
                $heads = Coa::where('general', 1)->orderBy('head_name', 'asc')->get();
            } else {
                $heads = Coa::whereNull('parent_id')->orderBy('head_name', 'asc')->get();
            }
            return response()->json(['status' => 'success', 'heads' => $heads]);
        }

        if (!is_null($request->print)) {
            $query = AccountTransaction::query();
            $voucher_type = $request->voucher_type;
            $date_range = explode('to', $request->date_range);
            $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
            $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');
            if (!is_null($voucher_type)) {
                $query->where('voucher_type', $voucher_type);
            }
            if (!is_null($start_date) && !is_null($end_date)) {
                $query->where('date', '>=', $start_date)->where('date', '<=', $end_date);
            }

            $data = $query->select('*', DB::raw('SUM(debit_amount) as amount'))->orderBY('id', 'desc')
                ->orderBY('date', 'desc')
                ->groupBy('voucher_no')->get();

            $report_title = 'Voucher List';
            $pdf = Pdf::loadView('admin.reports.voucher-list.print', compact('report_title', 'data'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('voucher_list_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Voucher List';
        $voucher_types = AccountTransaction::groupBy('voucher_type')->orderBy('voucher_type', 'asc')->get();
        return $dataTable->render('admin.reports.voucher-list.index', compact('title', 'voucher_types'));
    }

    public function cashBook(Request $request)
    {
        $previousBalance = 0;
        $data = array();
        $coa_id = $request->coa_id;
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');
        if ($request->has('filter')) {
            if (!is_null($request->coa_id)) {
                $previousBalance = AccountTransaction::where('coa_id', $request->coa_id)->where('date', '<', $start_date)->sum(DB::raw('debit_amount - credit_amount'));
                $data = AccountTransaction::where('coa_id', $request->coa_id)->where('date', '>=', $start_date)->where('date', '<=', $end_date)->orderBy('date', 'asc')->get();
            }
        }

        if (!is_null($request->print)) {
            $report_title = 'Cash book Report <br> <span class="text-sm">' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '</span>';
            $pdf = Pdf::loadView('admin.reports.cash-book.print', compact('report_title', 'previousBalance', 'data'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('cash_book_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Cash Book';
        $coas = Coa::where('transaction', 1)->where('head_code', 'LIKE', '10102%')->orderBy('head_name', 'asc')->get();
        return view('admin.reports.cash-book.index', compact('title', 'coas', 'start_date', 'end_date', 'previousBalance', 'data'));
    }

    public function bankBook(Request $request)
    {
        $previousBalance = 0;
        $data = array();
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');
        if ($request->has('filter')) {
            if (!is_null($request->coa_id)) {
                $previousBalance = AccountTransaction::where('coa_id', $request->coa_id)->where('date', '<', $start_date)->sum(DB::raw('debit_amount - credit_amount'));
                $data = AccountTransaction::where('coa_id', $request->coa_id)->where('date', '>=', $start_date)->where('date', '<=', $end_date)->orderBy('date', 'asc')->get();
            }
        }

        if (!is_null($request->print)) {
            $report_title = 'Bank book Report <br> <span class="text-sm">' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '</span>';
            $pdf = Pdf::loadView('admin.reports.bank-book.print', compact('report_title', 'previousBalance', 'data'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('bank_book_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Bank Book';
        $coas = Coa::where('transaction', 1)->where('head_code', 'LIKE', '10103%')->orderBy('head_name', 'asc')->get();
        return view('admin.reports.bank-book.index', compact('title', 'coas', 'start_date', 'end_date', 'previousBalance', 'data'));
    }

    public function transactionLedger(Request $request)
    {
        $previousBalance = 0;
        $data = array();
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');
        if ($request->has('filter')) {
            if (!is_null($request->coa_id)) {
                $previousBalance = AccountTransaction::where('coa_id', $request->coa_id)->where('date', '<', $start_date)->sum(DB::raw('debit_amount - credit_amount'));
                $data = AccountTransaction::where('coa_id', $request->coa_id)->where('date', '>=', $start_date)->where('date', '<=', $end_date)->orderBy('date', 'asc')->get();
            }
        }

        if (!is_null($request->print)) {
            $report_title = 'Transaction Ledger Report <br> <span class="text-sm">' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '</span>';
            $pdf = Pdf::loadView('admin.reports.transaction-ledger.print', compact('report_title', 'previousBalance', 'data'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('transaction_ledger_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Transaction Ledger';
        $coas = Coa::where('transaction', 1)->orderBy('head_name', 'asc')->get();
        return view('admin.reports.transaction-ledger.index', compact('title', 'coas', 'start_date', 'end_date', 'previousBalance', 'data'));
    }

    public function generalLedger(Request $request)
    {
        $previousBalance = 0;
        $data = array();
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');
        if ($request->has('filter')) {
            if (!is_null($request->coa_id)) {
                $previousBalance = AccountTransaction::whereHas('coa', function ($query) use ($request) {
                    $query->where('parent_id', $request->coa_id);
                })->where('date', '<', $start_date)->sum(DB::raw('debit_amount - credit_amount'));
                $data = AccountTransaction::whereHas('coa', function ($query) use ($request) {
                    $query->where('parent_id', $request->coa_id);
                })->where('date', '>=', $start_date)->where('date', '<=', $end_date)->orderBy('date', 'asc')->get();
            }
        }

        if (!is_null($request->print)) {
            $report_title = 'General Ledger Report <br> <span class="text-sm">' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '</span>';
            $pdf = Pdf::loadView('admin.reports.general-ledger.print', compact('report_title', 'previousBalance', 'data'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('general_ledger_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'General Ledger';
        $coas = Coa::where('general', 1)->orderBy('head_name', 'asc')->get();
        return view('admin.reports.general-ledger.index', compact('title', 'coas', 'start_date', 'end_date', 'previousBalance', 'data'));
    }

    public function cashFlowStatement(Request $request)
    {
        $data = array();
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');
        $generalLedgerHeadCash = 10102;
        $generalLedgerHeadBank = 10103;

        if ($request->has('filter')) {
            $data = AccountTransaction::where('date', '>=', $start_date)->where('date', '<=', $end_date)->groupBy('date')->orderBy('date', 'desc')->get('date');
        }

        if (!is_null($request->print)) {
            $report_title = 'Cash Flow Statement <br> <span class="text-sm">' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '</span>';;
            $pdf = Pdf::loadView('admin.reports.cash-flow-statement.print', compact('report_title', 'data', 'generalLedgerHeadCash', 'generalLedgerHeadBank'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('cash_flow_statement_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Cash Flow Statement';
        return view('admin.reports.cash-flow-statement.index', compact('title', 'start_date', 'end_date', 'data', 'generalLedgerHeadCash', 'generalLedgerHeadBank'));
    }

    public function trialBalance(Request $request)
    {
        $coaLists = array();
        $coaLists1 = array();
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');
        if ($request->has('filter')) {
            $coaLists = TrialBalance::with('coa')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->where('transaction', 1)->where('general', 1)->groupBy('coa_id')->select('*', DB::raw('SUM(debit_amount) as debit_amount'), DB::raw('SUM(credit_amount) as credit_amount'))->get();
            $coaLists1 = TrialBalance::with('parent_head')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->where('transaction', 1)->where('general', 0)->groupBy('parent_id')->select('*', DB::raw('SUM(debit_amount) as debit_amount'), DB::raw('SUM(credit_amount) as credit_amount'))->get();
        }

        if (!is_null($request->print)) {
            $report_title = 'Trial Balance <br> <span class="text-sm">' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '</span>';
            $pdf = Pdf::loadView('admin.reports.trial-balance.print', compact('report_title', 'coaLists', 'coaLists1'));
            return $pdf->stream('trial_balance_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Trial Balance';
        return view('admin.reports.trial-balance.index', compact('title', 'start_date', 'end_date', 'coaLists', 'coaLists1'));
    }

    public function incomeStatement(Request $request)
    {
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');

        $incomes = array();
        $expenses = array();

        //MyCode when run ageneral accounting comment this code
         if ($request->has('filter')) {
            $incomes = AccountTransactionAuto::with('coa')
                ->where('date', '>=', $start_date)
                ->where('date', '<=', $end_date)
                ->whereHas('coa', fn($q) => $q->where('head_type', 'A'))
                ->where('voucher_type', '=', 'Client Collection')
                ->where('debit_amount', 0.00)
                ->groupBy('coa_id')
                ->select('coa_head_code', 'coa_id', DB::raw('SUM(debit_amount) as debit_amount'), DB::raw('SUM(credit_amount) as credit_amount'))
                ->get();

            $expenses = AccountTransactionAuto::with('coa')->select('*', DB::raw('SUM(debit_amount - credit_amount) as amount'))
                ->where('date', '>=', $start_date)
                ->where('date', '<=', $end_date)
                ->whereHas('coa', fn($q) => $q->where('head_type', 'E')->where('transaction', 1))
                ->groupBy('coa_id')
                ->get();
        }

        //Old code when run ageneral accounting uncomment this code

        // if ($request->has('filter')) {
        //     $incomes = AccountTransaction::with('coa')
        //         ->where('date', '>=', $start_date)
        //         ->where('date', '<=', $end_date)
        //         ->whereHas('coa', fn($q) => $q->where('head_type', 'I'))
        //         ->groupBy('coa_id')
        //         ->select('coa_head_code', 'coa_id', DB::raw('SUM(debit_amount) as debit_amount'), DB::raw('SUM(credit_amount) as credit_amount'))
        //         ->get();

        //     $expenses = AccountTransaction::with('coa')->select('*', DB::raw('SUM(debit_amount - credit_amount) as amount'))
        //         ->where('date', '>=', $start_date)
        //         ->where('date', '<=', $end_date)
        //         ->whereHas('coa', fn($q) => $q->where('head_type', 'E')->where('transaction', 1))
        //         ->groupBy('coa_id')
        //         ->get();
        // }

        if (!is_null($request->print)) {
            $report_title = 'Income Statement Report <br> <span class="text-sm">' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '</span>';
            $pdf = Pdf::loadView('admin.reports.income-statement.print', compact('report_title', 'incomes', 'expenses'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('income_statement_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Income Statement';
        $print_buttons = '<button type="button" class="btn btn-sm btn-primary text-uppercase getPdf">Print</button>';
        return view('admin.reports.income-statement.index', compact('title', 'start_date', 'end_date', 'incomes', 'expenses', 'print_buttons'));
    }

    public function headDetails(Request $request)
    {
        if ($request->has('income_statement')) {
            $title = 'Head Transactions';
            $data = AccountTransaction::with('coa')
                ->where('date', '>=', $request->start_date)
                ->where('date', '<=', $request->end_date)
                ->where('coa_id', $request->coa_id)
                ->orderBy('date', 'asc')
                ->get();

            if ($request->has('details_print')) {
                $report_title = 'Head Transactions Details <br> <span class="text-sm">' . date('d-m-Y', strtotime($request->start_date)) . ' To ' . date('d-m-Y', strtotime($request->end_date)) . '</span>';
                $pdf = Pdf::loadView('admin.reports.income-statement.details_print', compact('report_title', 'data'));
                return $pdf->stream('transaction_details' . date('d_m_Y_h_i_s') . '.pdf');
            }
            return view('admin.reports.income-statement.details', compact('title', 'data'));
        }
        if ($request->has('balance_sheet')) {
            $title = 'Head Transactions';
            $data = AccountTransaction::with('coa')
                ->where('coa_id', $request->coa_id)
                ->orderBy('date', 'asc')
                ->get();

            if ($request->has('details_print')) {
                $report_title = 'Head Transactions Details <br> <span class="text-sm">' . date('d-m-Y', strtotime($request->start_date)) . ' To ' . date('d-m-Y', strtotime($request->end_date)) . '</span>';
                $pdf = Pdf::loadView('admin.reports.income-statement.details_print', compact('report_title', 'data'));
                return $pdf->stream('transaction_details' . date('d_m_Y_h_i_s') . '.pdf');
            }
            return view('admin.reports.income-statement.details', compact('title', 'data'));
        }

        $title = 'Head Transactions';
        $coa_ids = Coa::where('parent_id', $request->id)->pluck('id')->toArray();
        $child_coa_ids = Coa::whereIn('parent_id', $coa_ids)->pluck('id')->toArray();
        $coa_ids = array_merge($coa_ids, $child_coa_ids);
        $data = AccountTransaction::with('coa')->select('*', DB::raw('SUM(debit_amount) as debit_amount'), DB::raw('SUM(credit_amount) as credit_amount'))
            ->whereIn('coa_id', $coa_ids)
            ->groupBy('coa_id')
            ->orderBy('date', 'asc')
            ->get();

        if ($request->has('details_print')) {
            $report_title = 'Head Transactions Details';
            $pdf = Pdf::loadView('admin.reports.balance-sheet.details_print', compact('report_title', 'data'));
            return $pdf->stream('transaction_details' . date('d_m_Y_h_i_s') . '.pdf');
        }
        return view('admin.reports.balance-sheet.details', compact('title', 'data'));
    }

    public function balanceSheet(Request $request)
    {
        $assets = $this->assets('Assets');
        $liabilities = $this->assets('Liabilities');
        $currentPFL = $this->profitLoss();
        $data = [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'currentPFL' => $currentPFL,
        ];

        if (!is_null($request->print)) {
            $report_title = 'Balance Sheet';
            $pdf = Pdf::loadView('admin.reports.balance-sheet.print', compact('report_title', 'data'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('balance_sheet_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Balance Sheet';
        $coas = Coa::where('general', 1)->orderBy('head_name', 'asc')->get();
        return view('admin.reports.balance-sheet.index', compact('title', 'coas', 'data'));
    }

    public function getAmount($headCode)
    {
        $balance = 0;
        $headReports = TrialBalance::select('*')
            ->where('coa_head_code', 'LIKE', $headCode . '%')
            ->get();

        foreach ($headReports as $headReport) {
            if ($headReport->head_type == 'I' || $headReport->head_type == 'L') {
                $balance += $headReport->credit_amount - $headReport->debit_amount;
            } else {
                $balance += $headReport->debit_amount - $headReport->credit_amount;
            }
        }
        return $balance;
    }

    public function assets($parent_head)
    {
        $parents = Coa::with('parent')->whereHas('parent', function ($query) use ($parent_head) {
            $query->where('head_name', $parent_head);
        })->orderBy('head_name', 'asc')->get();

        $info = [];
        foreach ($parents as $parent) {
            $childs = Coa::select('head_name', 'head_code', 'id')
                ->where('parent_id', $parent->id)
                ->get();
            $childInfo = [];
            foreach ($childs as $child) {
                $childInfo[] = [
                    'id' => $child->id,
                    'headCode' => $child->head_code,
                    'headName' => $parent->head_name,
                    'name' => $child->head_name,
                    'amount' => $this->getAmount($child->head_code)
                ];
            }

            $info[] = [
                'id' => $parent->id,
                'headCode' => $parent->head_code,
                'head' => $parent->head_name,
                'childs' => $childInfo
            ];
        }
        return $info;
    }

    private function profitLoss()
    {
        $totalIncome = trialBalance::where('head_type', 'I')->sum(DB::raw('credit_amount - debit_amount'));
        $totalExpanse = trialBalance::where('head_type', 'E')->sum(DB::raw('debit_amount - credit_amount'));
        return $totalIncome - $totalExpanse;
    }

    public function stockStatus(Request $request)
    {
        if ($request->ajax()) {
            $term = $request->get('q');
            $results = Product::where('name', 'LIKE', "%$term%")
                ->select('id', 'name', 'code')
                ->get();

            return response()->json($results);
        }

        $title = 'Stock Status';
        $data = [];
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');

        $store_ids = $request->has('store_id') ? (array)$request->store_id : Store::where('status', true)->pluck('id')->toArray();
        $product_ids = $request->has('product_id') ? (array)$request->product_id : Product::pluck('id')->toArray();

        if ($request->has('filter')) {
            // Get all product editions for selected products
            $editions = \App\Models\ProductEdition::whereIn('product_id', $product_ids)->get();
            $edition_ids = $editions->pluck('id')->toArray();
            $searched_products = $editions->map(function($edition) {
                $edition->product_name = $edition->product->name;
                $edition->edition_name = $edition->name;
                return $edition;
            });

            // Production (in)
            $productions = \App\Models\ProductionList::whereIn('product_edition_id', $edition_ids)
                ->whereIn('store_id', $store_ids)
                ->whereHas('production', function($q) use ($start_date, $end_date) {
                    $q->where('date', '>=', $start_date)->where('date', '<=', $end_date);
                })->get();

            // Sales (out)
            $sales = \App\Models\SalesList::whereIn('product_edition_id', $edition_ids)
                ->whereIn('store_id', $store_ids)
                ->whereHas('sales', function($q) use ($start_date, $end_date) {
                    $q->where('date', '>=', $start_date)->where('date', '<=', $end_date);
                })->get();

            // Sales Return (in)
            $sales_returns = \App\Models\SalesReturnList::whereIn('product_edition_id', $edition_ids)
                ->whereIn('store_id', $store_ids)
                ->whereHas('return', function($q) use ($start_date, $end_date) {
                    $q->where('date', '>=', $start_date)->where('date', '<=', $end_date);
                })->get();

            // Opening calculation (before start_date)
            $opening_productions = \App\Models\ProductionList::whereIn('product_edition_id', $edition_ids)
                ->whereIn('store_id', $store_ids)
                ->whereHas('production', function($q) use ($start_date) {
                    $q->where('date', '<', $start_date);
                })->get();
            $opening_sales = \App\Models\SalesList::whereIn('product_edition_id', $edition_ids)
                ->whereIn('store_id', $store_ids)
                ->whereHas('sales', function($q) use ($start_date) {
                    $q->where('date', '<', $start_date);
                })->get();
            $opening_sales_returns = \App\Models\SalesReturnList::whereIn('product_edition_id', $edition_ids)
                ->whereIn('store_id', $store_ids)
                ->whereHas('return', function($q) use ($start_date) {
                    $q->where('date', '<', $start_date);
                })->get();

            $data = [];
            foreach ($searched_products as $edition) {
                $prod_id = $edition->product_id;
                $edition_id = $edition->id;
                $product = Product::find($prod_id);
                $stock = $product->variants->sum('stock')??0;
                $row = [
                    'product' => $edition->product_name,
                    'edition' => $edition->edition_name,
                    'opening' =>
                        $opening_productions->where('product_edition_id', $edition_id)->sum('qty')
                        - $opening_sales->where('product_edition_id', $edition_id)->sum('qty')
                        + $opening_sales_returns->where('product_edition_id', $edition_id)->sum('qty'),
                    'production' => $productions->where('product_edition_id', $edition_id)->sum('qty'),
                    'sales' => $sales->where('product_edition_id', $edition_id)->sum('qty'),
                    'sales_return' => $sales_returns->where('product_edition_id', $edition_id)->sum('qty'),
                ];
                // $row['stock'] = $row['opening'] + $row['production'] - $row['sales'] + $row['sales_return'];
                 $row['stock']=$stock;
                $data[] = $row;
            }
        }

        if (!is_null($request->print)) {
            $report_title = 'Stock Status Report <br> <span class="text-sm">' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '</span>';
            $pdf = Pdf::loadView('admin.reports.stock-status.print', compact('report_title', 'data'));
            return $pdf->stream('stock_status_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $stores = Store::where('status', true)->orderBy('name', 'asc')->get();
        return view('admin.reports.stock-status.index', compact('title', 'stores', 'start_date', 'end_date', 'data', 'stores'));
    }

    public function investorStatement(Request $request)
    {
        $previous_balance = 0;
        $statements = [];
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');

        if ($request->has('filter')) {
            $dateRange = CarbonPeriod::create($start_date, $end_date);
            $investAmount = Invest::where('investor_id', $request->investor_id)->where('date', '<', $start_date)->where('sattled', false)->sum('amount');
            $profitAmount = ProfitDistributionList::where('investor_id', $request->investor_id)->whereHas('profitDistribution', function ($query) use ($start_date) {
                $query->where('date', '<', $start_date);
            })->sum('amount');
            $paymentAmount = Payment::where('investor_id', $request->investor_id)->where('date', '<', $start_date)->whereIn('payment_type', ['Payment', 'Advance'])->sum('amount');
            $previous_balance = $investAmount + $profitAmount - $paymentAmount;
            $balance = $previous_balance;
            foreach ($dateRange as $date) {
                $d = $date->format('Y-m-d');
                $invests = Invest::where('investor_id', $request->investor_id)->where('date', $d)->get();
                foreach ($invests as $item) {
                    $balance += $item->amount;
                    $row = [
                        'date' => $date->format('d-m-Y'),
                        'remarks' => 'Invest against invest no - ' . $item->invest_no,
                        'amount_in' => $item->amount,
                        'amount_out' => 0.00,
                        'balance' => $balance,
                    ];
                    array_push($statements, $row);
                }

                $profits = ProfitDistributionList::whereHas('profitDistribution', function ($query) use ($d) {
                    $query->where('date', $d);
                })->where('investor_id', $request->investor_id)->get();
                foreach ($profits as $item) {
                    $balance += $item->amount;
                    $row = [
                        'date' => $date->format('d-m-Y'),
                        'remarks' => 'Profit against serial no - ' . $item->profitDistribution->serial_no ?? '',
                        'amount_in' => $item->amount,
                        'amount_out' => 0.00,
                        'balance' => $balance,
                    ];
                    array_push($statements, $row);
                }

                $payments = Payment::where('date', $d)->where('investor_id', $request->investor_id)->whereIn('payment_type', ['Payment', 'Advance'])->get();
                foreach ($payments as $item) {
                    $balance -= $item->amount;
                    $row = [
                        'date' => $date->format('d-m-Y'),
                        'remarks' => 'Payment against payment no - ' . $item->payment_no,
                        'amount_in' => 0.00,
                        'amount_out' => $item->amount,
                        'balance' => $balance,
                    ];
                    array_push($statements, $row);
                }

                $sattlements = InvestSattlement::where('date', $d)->where('investor_id', $request->investor_id)->get();
                foreach ($sattlements as $item) {
                    $balance -= $item->payment;
                    $row = [
                        'date' => $date->format('d-m-Y'),
                        'remarks' => 'Invest Sattlement against Sattlement no - ' . $item->sattlement_no,
                        'amount_in' => 0.00,
                        'amount_out' => $item->payment,
                        'balance' => $balance,
                    ];
                    array_push($statements, $row);
                }
            }
        }

        if (!is_null($request->print)) {
            $investor = Investor::find($request->investor_id);
            $report_title = 'Investor Statement of - ' . $investor->name;
            $pdf = Pdf::loadView('admin.reports.investor-statement.print', compact('report_title', 'previous_balance', 'statements'));
            return $pdf->stream('investor_statement_' . date('d_m_Y_H_i_s') . '.pdf');
        }

        $title = 'Investor Statement';
        return view('admin.reports.investor-statement.index', compact('title', 'start_date', 'end_date', 'previous_balance', 'statements'));
    }


    public function salesReport(Request $request, InvoiceWiseSalesHistoryDataTable $invoiceHistoryDataTable, ProductWiseSalesHistroyDataTable $productHistoryDataTable, ProductWiseSalesSummaryDataTable $productSummaryDataTable)
    {
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');

        if (!is_null($request->print)) {
            $report_type = $request->report_type;
            if ($report_type == 'product_history') {
                $report_title = 'Product Wise Sales History On ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date));
                $query = SalesList::with(['sales', 'product'])->leftJoin('sales', 'sales.id', '=', 'sales_lists.sales_id');
                $query->whereHas('sales', function ($q) use ($start_date, $end_date) {
                    if (request('store_id')) {
                        $q->where('store_id', request('store_id'));
                    }
                    $q->where('date', '>=', $start_date)->where('date', '<=', $end_date);
                });
                if (request('product_id')) {
                    $query->whereIn('product_id', request('product_id'));
                }
                $data = $query->orderBy('sales.date', 'desc')->orderBy('id', 'desc')->select('sales_lists.id', 'sales_lists.product_id', 'sales_lists.qty', 'sales_lists.amount', 'sales_lists.sales_id')->get();
                // return view('admin.reports.sales-report.print', compact('report_title', 'report_type', 'data'));
                $pdf = Pdf::loadView('admin.reports.sales-report.print', compact('report_title', 'report_type', 'data'));
                $pdf->setPaper('A4');
                return $pdf->stream('product_wise_sales_history_' . date('d_m_Y_H_i_s') . '.pdf');
            } elseif ($report_type == 'product_summary') {
                $report_title = 'Product Wise Sales Summary On ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date));
                $query = SalesList::with(['sales', 'product']);
                $query->whereHas('sales', function ($q) use ($start_date, $end_date) {
                    if (request('store_id')) {
                        $q->where('store_id', request('store_id'));
                    }
                    $q->where('date', '>=', $start_date)->where('date', '<=', $end_date);
                });
                if (request('product_id')) {
                    $query->whereIn('product_id', request('product_id'));
                }
                $data = $query->groupBy('product_id')->select('*', DB::raw('SUM(qty) as sum_qty'), DB::raw('SUM(amount) as sum_amount'))->get();
                // return view('admin.reports.sales-report.print', compact('report_title', 'report_type', 'data'));
                $pdf = Pdf::loadView('admin.reports.sales-report.print', compact('report_title', 'report_type', 'data'));
                $pdf->setPaper('A4');
                return $pdf->stream('product_wise_sales_summary_' . date('d_m_Y_H_i_s') . '.pdf');
            } else {
                $report_title = 'Invoice Wise Sales History On ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date));
                $query = Sales::with(['store', 'list']);
                if ($request->store_id) {
                    $query->where('store_id', $request->store_id);
                }
                $query->whereHas('list', function ($q) use ($request) {
                    if ($request->product_id) {
                        $q->whereIn('product_id', $request->product_id);
                    }
                });
                $query->where('date', '>=', $start_date)->where('date', '<=', $end_date);
                $data = $query->orderBy('date', 'desc')->get();
                // return view('admin.reports.sales-report.print', compact('report_title', 'report_type', 'data'));
                $pdf = Pdf::loadView('admin.reports.sales-report.print', compact('report_title', 'report_type', 'data'));
                $pdf->setPaper('A3');
                return $pdf->stream('invoice_wise_sales_history_' . date('d_m_Y_H_i_s') . '.pdf');
            }
        }

        $products = Product::where('status', true)->orderBy('name', 'asc')->get();
        $report_type = $request->report_type;
        if ($report_type == 'product_history') {
            $title = 'Product Wise History';
            return $productHistoryDataTable->render('admin.reports.sales-report.index', compact('title', 'products', 'start_date', 'end_date'));
        } elseif ($report_type == 'product_summary') {
            $title = 'Product Wise Summary';
            return $productSummaryDataTable->render('admin.reports.sales-report.index', compact('title', 'products', 'start_date', 'end_date'));
        } else {
            $title = 'Invoice Wise History';
            return $invoiceHistoryDataTable->render('admin.reports.sales-report.index', compact('title', 'products', 'start_date', 'end_date'));
        }
    }

    public function collectionReport(Request $request, InvoiceWiseCollectionHistoryDataTable $invoiceHistoryDataTable, ClientWiseCollectionSummaryDataTable $clientSummaryDataTable)
    {
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');

        if (!is_null($request->print)) {
            $report_type = $request->report_type;
            if ($report_type == 'client_summary') {
                $report_title = 'Client Wise Collection Summary On ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date));

                $query = Collection::with(['client']);
                if ($request->client_id) {
                    $query->whereIn('client_id', $request->client_id);
                }
                $query->where('date', '>=', $start_date)->where('date', '<=', $end_date);
                $data = $query->whereNotIn('type', ['Return', 'Adjust'])->groupBy('client_id')->select('*', DB::raw('SUM(amount) as sum_amount'))->get();
                // return view('admin.reports.collection-report.print', compact('report_title', 'report_type', 'data'));
                $pdf = Pdf::loadView('admin.reports.collection-report.print', compact('report_title', 'report_type', 'data'));
                $pdf->setPaper('A4');
                return $pdf->stream('client_wise_collection_summary_' . date('d_m_Y_H_i_s') . '.pdf');
            } else {
                $report_title = 'Invoice Wise Collection History On ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date));
                $query = Collection::with(['client']);
                if ($request->client_id) {
                    $query->whereIn('client_id', $request->client_id);
                }
                $query->where('date', '>=', $start_date)->where('date', '<=', $end_date);
                $data = $query->whereNotIn('type', ['Return', 'Adjust'])->orderBy('date', 'desc')->get();
                // return view('admin.reports.collection-report.print', compact('report_title', 'report_type', 'data'));
                $pdf = Pdf::loadView('admin.reports.collection-report.print', compact('report_title', 'report_type', 'data'));
                $pdf->setPaper('A4');
                return $pdf->stream('invoice_wise_collection_history_' . date('d_m_Y_H_i_s') . '.pdf');
            }
        }

        $clients = Client::where('status', true)->orderBy('name', 'asc')->get();
        $report_type = $request->report_type;
        if ($report_type == 'client_summary') {
            $title = 'Collection Summary';
            return $clientSummaryDataTable->render('admin.reports.collection-report.index', compact('title', 'clients', 'start_date', 'end_date'));
        } else {
            $title = 'Collection History';
            return $invoiceHistoryDataTable->render('admin.reports.collection-report.index', compact('title', 'clients', 'start_date', 'end_date'));
        }
    }

    public function salesReturnReport(Request $request, InvoiceWiseSalesReturnHistoryDataTable $invoiceHistoryDataTable, ProductWiseSalesReturnHistoryDataTable $productHistoryDataTable, ProductWiseSalesReturnSummaryDataTable $productSummaryDataTable)
    {
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');

        if (!is_null($request->print)) {
            $report_type = $request->report_type;
            if ($report_type == 'product_history') {
                $report_title = 'Product Wise Return History On ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date));
                $query = SalesReturnList::with(['purchase_return', 'store', 'client'])->leftJoin('sales_returns', 'sales_returns.id', '=', 'sales_return_lists.sales_return_id');
                $query->whereHas('sales_return', function ($q) use ($start_date, $end_date) {
                    if (request('store_id')) {
                        $q->where('store_id', request('store_id'));
                    }
                    if (request('client_id')) {
                        $q->whereIn('client_id', request('client_id'));
                    }
                    $q->where('date', '>=', $start_date)->where('date', '<=', $end_date);
                });
                if (request('product_id')) {
                    $query->whereIn('product_id', request('product_id'));
                }
                $data = $query->orderBy('sales_returns.date', 'desc')->orderBy('id', 'desc')->select('sales_return_lists.*')->get();
                // return view('admin.reports.sales-return-report.print', compact('report_title', 'report_type', 'data'));
                $pdf = Pdf::loadView('admin.reports.sales-return-report.print', compact('report_title', 'report_type', 'data'));
                $pdf->setPaper('A3');
                return $pdf->stream('product_wise_sales_return_history_' . date('d_m_Y_H_i_s') . '.pdf');
            } elseif ($report_type == 'product_summary') {
                $report_title = 'Product Wise Return Summary On ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date));
                $query = SalesReturnList::with(['sales_return', 'store', 'client']);
                $query->whereHas('sales_return', function ($q) use ($start_date, $end_date) {
                    if (request('store_id')) {
                        $q->where('store_id', request('store_id'));
                    }
                    if (request('client_id')) {
                        $q->whereIn('client_id', request('client_id'));
                    }
                    $q->where('date', '>=', $start_date)->where('date', '<=', $end_date);
                });
                if (request('product_id')) {
                    $query->whereIn('product_id', request('product_id'));
                }
                $data = $query->groupBy('product_id')->select('*', DB::raw('SUM(qty) as sum_quantity'), DB::raw('SUM(amount) as sum_amount'))->get();
                // return view('admin.reports.sales-return-report.print', compact('report_title', 'report_type', 'data'));
                $pdf = Pdf::loadView('admin.reports.sales-return-report.print', compact('report_title', 'report_type', 'data'));
                $pdf->setPaper('A4');
                return $pdf->stream('product_wise_sales_return_summary_' . date('d_m_Y_H_i_s') . '.pdf');
            } else {
                $report_title = 'Invoice Wise Return History On ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date));
                $query = SalesReturn::with(['store', 'client']);
                if ($request->store_id) {
                    $query->where('store_id', $request->store_id);
                }
                if ($request->client_id) {
                    $query->whereIn('client_id', $request->client_id);
                }
                $query->whereHas('list', function ($q) use ($request) {
                    if ($request->product_id) {
                        $q->whereIn('product_id', $request->product_id);
                    }
                });
                $query->where('date', '>=', $start_date)->where('date', '<=', $end_date);
                $data = $query->orderBy('date', 'desc')->get();
                // return view('admin.reports.sales-return-report.print', compact('report_title', 'report_type', 'data'));
                $pdf = Pdf::loadView('admin.reports.sales-return-report.print', compact('report_title', 'report_type', 'data'));
                $pdf->setPaper('A2');
                return $pdf->stream('invoice_wise_sales_return_history_' . date('d_m_Y_H_i_s') . '.pdf');
            }
        }

        $products   = Product::where('status', true)->orderBy('name', 'asc')->get();
        $stores     = Store::where('status', true)->orderBy('name', 'asc')->get();
        $clients    = Client::where('status', true)->orderBy('name', 'asc')->get();

        $report_type = $request->report_type;
        if ($report_type == 'product_history') {
            $title = 'Product Wise History';
            return $productHistoryDataTable->render('admin.reports.sales-return-report.index', compact('title', 'stores', 'clients', 'products', 'start_date', 'end_date'));
        } elseif ($report_type == 'product_summary') {
            $title = 'Product Wise Summary';
            return $productSummaryDataTable->render('admin.reports.sales-return-report.index', compact('title', 'stores', 'clients', 'products', 'start_date', 'end_date'));
        } else {
            $title = 'Invoice Wise History';
            return $invoiceHistoryDataTable->render('admin.reports.sales-return-report.index', compact('title', 'stores', 'clients', 'products', 'start_date', 'end_date'));
        }
    }
}
