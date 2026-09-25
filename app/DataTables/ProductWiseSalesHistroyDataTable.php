<?php

namespace App\DataTables;

use App\Models\SalesList;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductWiseSalesHistroyDataTable extends DataTable
{
    protected float $totalAmount = 0;

    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()

            ->editColumn('amount', function ($row) {
                return number_format($row->net_amount, 2);
            })

            // 🔥 send fixed total to frontend
            ->with([
                'net_amount' => number_format($this->totalAmount, 2),
            ]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(SalesList $model): QueryBuilder
    {
        $dateRange = explode('to', request('date_range') ?? '');

        $startDate = request('date_range')
            ? date('Y-m-d', strtotime(trim($dateRange[0])))
            : date('Y-m-01');

        $endDate = request('date_range')
            ? date('Y-m-d', strtotime(trim($dateRange[1])))
            : date('Y-m-t');

        $query = $model
            ->with(['sales', 'product'])
            ->leftJoin('sales', 'sales.id', '=', 'sales_lists.sales_id')
            ->whereBetween('sales.date', [$startDate, $endDate]);

        if (request('store_id')) {
            $query->where('sales.store_id', request('store_id'));
        }

        if (request('product_id')) {
            $query->whereIn('sales_lists.product_id', (array) request('product_id'));
        }

        // 🔥 calculate total BEFORE pagination
        $this->totalAmount = (clone $query)->sum('sales_lists.net_amount');

        return $query
            ->orderBy('sales.date', 'desc')
            ->orderBy('sales_lists.id', 'desc')
            ->select([
                'sales_lists.id',
                'sales_lists.product_id',
                'sales_lists.qty',
                'sales_lists.net_amount',
                'sales_lists.sales_id',
            ]);
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

                // ✅ correct footer update
                'drawCallback' => 'function () {
                    let json = this.api().ajax.json();
                    $("#net_amount").html(json.net_amount);
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
                'data' => 'DT_RowIndex',
                'name' => 'DT_RowIndex',
                'title' => 'SL#',
                'orderable' => false,
                'searchable' => false,
                'class' => 'text-center',
                'width' => '20',
            ]),
            Column::make([
                'data' => 'sales.formattedDate',
                'name' => 'sales.formattedDate',
                'title' => 'Date',
                'orderable' => false,
                'searchable' => false,
                'defaultContent' => '',
            ]),
            Column::make([
                'data' => 'sales.invoice',
                'name' => 'sales.invoice',
                'title' => 'Invoice',
                'defaultContent' => '',
            ]),
            Column::make([
                'data' => 'product.name',
                'name' => 'product.name',
                'title' => 'Product',
            ]),
            Column::make([
                'data' => 'qty',
                'name' => 'qty',
                'title' => 'Quantity',
                'class' => 'text-end',
            ]),
            Column::make([
                'data' => 'amount',
                'name' => 'amount',
                'title' => 'Amount',
                'class' => 'text-end',
                'footer' => '<strong id="net_amount"></strong>',
            ]),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'product_wise_sales_histroy_' . now()->format('d_m_Y_h_i_s_A');
    }
}
