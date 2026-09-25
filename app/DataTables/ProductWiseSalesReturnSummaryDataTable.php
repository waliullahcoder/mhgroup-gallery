<?php

namespace App\DataTables;

use App\Models\SalesReturnList;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductWiseSalesReturnSummaryDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('sum_qty', fn($row) => number_format($row->sum_qty))
            ->editColumn('sum_amount', fn($row) => number_format($row->sum_amount, 2))
            ->with([
                'total_quantity' => number_format(
                    (clone $query)->sum('qty'),
                    2
                ),
                'total_amount' => number_format(
                    (clone $query)->sum('amount'),
                    2
                ),
            ]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(SalesReturnList $model): QueryBuilder
    {
        $date_range = explode('to', request('date_range'));
        $start_date = request('date_range')
            ? date('Y-m-d', strtotime($date_range[0]))
            : date('Y-m-01');

        $end_date = request('date_range')
            ? date('Y-m-d', strtotime($date_range[1]))
            : date('Y-m-t');

        $query = $model
            ->with(['product'])
            ->whereHas('return', function ($q) use ($start_date, $end_date) {
                if (request('store_id')) {
                    $q->where('store_id', request('store_id'));
                }

                if (request('client_id')) {
                    $q->whereIn('client_id', request('client_id'));
                }

                $q->whereBetween('date', [$start_date, $end_date]);
            });

        if (request('product_id')) {
            $query->whereIn('product_id', request('product_id'));
        }

        return $query
            ->groupBy('sales_return_lists.product_id')
            ->select(
                '*',
                DB::raw('SUM(sales_return_lists.qty) as sum_qty'),
                DB::raw('SUM(sales_return_lists.amount) as sum_amount')
            );
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
            ->dom('Bfrtip')
            ->selectStyleSingle()
            ->parameters([
                'buttons' => [
                    Button::make('reload'),
                    [
                        'extend' => 'excel',
                        'text'   => '<i class="fal fa-file-spreadsheet"></i> Excel',
                    ],
                    [
                        'text'      => '<i class="fal fa-file-pdf"></i> Print',
                        'className' => 'getPdf',
                    ],
                ],
                'responsive' => true,
                'drawCallback' => 'function () {
                    let json = this.api().ajax.json();
                    $("#total_quantity").html(json.total_quantity);
                    $("#total_amount").html(json.total_amount);
                }',
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')
                ->title('SL#')
                ->orderable(false)
                ->searchable(false)
                ->class('text-center')
                ->width(10),

            Column::make('product.name')
                ->title('Product')
                ->defaultContent('')
                ->class('text-nowrap'),

            Column::make('sum_qty')
                ->title('Quantity')
                ->orderable(false)
                ->searchable(false)
                ->class('text-end')
                ->footer('<span id="total_quantity"></span>'),

            Column::make('sum_amount')
                ->title('Amount')
                ->orderable(false)
                ->searchable(false)
                ->class('text-end')
                ->footer('<span id="total_amount"></span>'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'product_wise_sales_return_summary_' . date('d_m_Y_h_i_s_A');
    }
}
