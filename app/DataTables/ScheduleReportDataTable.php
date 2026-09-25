<?php

namespace App\DataTables;

use App\Models\PaymentSchedule;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ScheduleReportDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('type', function ($row) {
                if ($row->is_last) {
                    return 'Sattlement';
                }
                return 'Advance Profit';
            })
            ->addColumn('action', function ($row) {
                if ($row->paid) {
                    return '<span class="btn btn-xs btn-success">Paid</span>';
                }
                if (!$row->is_last) {
                    return '<a href="' . ($row->schedule_date <= date('Y-m-d') ? Route('admin.schedule-payment.create') . '?schedule_id=' . $row->id . '&investor_id=' . $row->investor_id . '&invest_id=' . $row->invest_id : 'javascript:void(0)') . '" class="btn btn-xs btn-danger" title="Make Payment" data-bs-original-title="Make Payment">Payment</a>';
                }
                return '<a href="' . ($row->schedule_date <= date('Y-m-d') ? Route('admin.invest-sattlement.create') . '?schedule_id=' . $row->id . '&investor_id=' . $row->investor_id . '&invest_id=' . $row->invest_id  : 'javascript:void(0)') . '" class="btn btn-xs btn-danger" title="Make Sattlement" data-bs-original-title="Make Sattlement">Sattlement</a>';
            })
            ->setRowId('id')
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Request $request, PaymentSchedule $model): QueryBuilder
    {
        $query = $model->with(['investor', 'invest']);
        $query->where('schedule_year', $request->year ?? date('Y'));
        $query->where('schedule_month', $request->month ?? date('F'));
        return $query->orderBy('schedule_date', 'asc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('dataTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('lBfrtip')
            ->selectStyleSingle()
            ->parameters([
                'buttons'      => [
                    Button::make('reload'),
                    [
                        'extend'  => 'excel',
                        'text'    => '<i class="fal fa-file-spreadsheet"></i> Excel',
                    ],
                    [
                        'text'    => '<i class="fal fa-file-pdf"></i> Print',
                        'className' => 'getPdf',
                    ],
                ],
                'responsive' => true,
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make([
                'data'      => "DT_RowIndex",
                'name'      => "DT_RowIndex",
                'title'     => 'SL#',
                'orderable' => false,
                'searchable' => false,
                'width'     => '20',
                'class'     => 'text-center',
            ]),
            Column::make([
                'data'      => 'formattedDate',
                'name'      => 'formattedDate',
                'title'     => 'Schedule Date',
                'class'     => 'text-nowrap',
                'orderable' => false,
                'searchable' => false,
            ]),
            Column::make([
                'data'      => 'investor.name',
                'name'      => 'investor.name',
                'title'     => 'Investor',
                'defaultContent' => '',
            ]),
            Column::make([
                'data'      => 'investor.phone',
                'name'      => 'investor.phone',
                'title'     => 'Phone',
                'defaultContent' => '',
            ]),
            Column::make([
                'data'      => 'investor.bkash',
                'name'      => 'investor.bkash',
                'title'     => 'bKash',
                'defaultContent' => '',
            ]),
            Column::make([
                'data'      => 'investor.bank',
                'name'      => 'investor.bank',
                'title'     => 'Bank',
                'defaultContent' => '',
            ]),
            Column::make([
                'data'      => 'investor.account_name',
                'name'      => 'investor.account_name',
                'title'     => 'Account Name',
                'defaultContent' => '',
            ]),
            Column::make([
                'data'      => 'investor.account_no',
                'name'      => 'investor.account_no',
                'title'     => 'Account No',
                'defaultContent' => '',
            ]),
            Column::make([
                'data'      => 'type',
                'name'      => 'type',
                'title'     => 'Schedule Type',
            ]),
            Column::make([
                'data'      => 'amount',
                'name'      => 'amount',
                'title'     => 'Amount',
            ]),
            Column::make([
                'data'      => 'action',
                'name'      => 'action',
                'title'     => 'Status',
                'orderable' => false,
                'searchable' => false,
                'width'     => '100',
                'class'     => 'text-end',
            ]),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'schedule_report_' . date('d_m_Y_h_i_s_A');
    }
}
