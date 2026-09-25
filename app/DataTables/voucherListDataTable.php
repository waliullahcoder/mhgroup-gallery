<?php

namespace App\DataTables;

use App\Models\AccountTransaction;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class voucherListDataTable extends DataTable
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
            ->editColumn('date', fn($row) => date('d-m-Y', strtotime($row->date)))
            ->addColumn('debit_head', function ($row) {
                return AccountTransaction::with('coa')
                    ->where('voucher_no', $row->voucher_no)
                    ->where('voucher_type', $row->voucher_type)
                    ->where('debit_amount', '>', 0)
                    ->get()
                    ->pluck('coa.head_name')
                    ->implode(', ');
            })
            ->addColumn('credit_head', function ($row) {
                return AccountTransaction::with('coa')
                    ->where('voucher_no', $row->voucher_no)
                    ->where('voucher_type', $row->voucher_type)
                    ->where('credit_amount', '>', 0)
                    ->get()
                    ->pluck('coa.head_name')
                    ->implode(', ');
            });
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Request $request, AccountTransaction $model): QueryBuilder
    {
        $query = $model->query();

        $voucher_type = $request->voucher_type;
        $date_range = explode('to', $request->date_range);
        $start_date = $request->date_range ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = $request->date_range ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');

        if ($voucher_type) {
            $query->where('voucher_type', $voucher_type);
        }

        return $query->select([
            DB::raw('MAX(id) as id'),
            'voucher_no',
            'voucher_type',
            DB::raw('MAX(date) as date'),
            DB::raw('SUM(debit_amount) as amount'),
            DB::raw('MAX(created_at) as created_at'),
        ])
            ->whereBetween('date', [$start_date, $end_date])
            ->groupBy('voucher_no', 'voucher_type')
            ->orderByRaw('MAX(date) DESC');
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
                        'text'    => '<i class="fal fa-file-spreadsheet"></i> Exel',
                    ],
                    [
                        'text'    => '<i class="fal fa-file-pdf"></i> Print',
                        'className' => 'getPdf',
                    ],
                ],
                'responsive' => true,
                'drawCallback' => 'function() {
                    let data = this.api().ajax.json().data;
                    var total_amount = 0;
                    data.forEach(function(item, index){
                        total_amount += parseFloat(item.amount ?? 0);
                    });
                    $("#total_amount").html(total_amount.toFixed(2));
                }',
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
                'data'      => "formattedDate",
                'name'      => "formattedDate",
                'title'     => 'Date',
                'orderable' => false,
                'searchable' => false,
            ]),
            Column::make([
                'data'      => "voucher_type",
                'name'      => "voucher_type",
                'title'     => 'Voucher Type',
            ]),
            Column::make([
                'data'      => "voucher_no",
                'name'      => "voucher_no",
                'title'     => 'Voucher No',
            ]),
            Column::make([
                'data'      => "debit_head",
                'name'      => "debit_head",
                'title'     => 'Debit Head',
            ]),
            Column::make([
                'data'      => "credit_head",
                'name'      => "credit_head",
                'title'     => 'Credit Head',
            ]),
            Column::make([
                'data'      => "amount",
                'name'      => "amount",
                'title'     => 'Amount',
                'footer'    => '<span id="total_amount"></span>',
            ]),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'voucher_list_' . date('d_m_Y_h_i_s_A');
    }
}
