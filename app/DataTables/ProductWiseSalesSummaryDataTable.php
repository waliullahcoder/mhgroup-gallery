<?php

namespace App\DataTables;

use App\Models\SalesList;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductWiseSalesSummaryDataTable extends DataTable
{
    protected float $totalQty = 0;
    protected float $totalAmount = 0;

    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()

            ->editColumn('sum_qty', function ($row) {
                return number_format($row->sum_qty, 2);
            })

            ->editColumn('sum_amount', function ($row) {
                return number_format($row->sum_amount, 2);
            })

            // 🔥 send fixed totals
            ->with([
                'total_qty'    => number_format($this->totalQty, 2),
                'net_amount'   => number_format($this->totalAmount, 2),
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

        $query = $model->with(['product', 'sales'])
            ->whereHas('sales', function ($q) use ($startDate, $endDate) {
                if (request('store_id')) {
                    $q->where('store_id', request('store_id'));
                }
                $q->whereBetween('date', [$startDate, $endDate]);
            });

        if (request('product_id')) {
            $query->whereIn('product_id', (array) request('product_id'));
        }

        $query->groupBy('product_id')
            ->select([
                'product_id',
                DB::raw('SUM(qty) as sum_qty'),
                DB::raw('SUM(net_amount) as sum_amount'),
            ]);

        // 🔥 calculate totals BEFORE pagination
        $totals = (clone $query)->get();

        $this->totalQty    = $totals->sum('sum_qty');
        $this->totalAmount = $totals->sum('sum_amount');

        return $query;
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
                        'text'       => '<i class="fal fa-file-pdf"></i> Print',
                        'className'  => 'getPdf',
                    ],
                ],
                'responsive' => true,

                // ✅ fixed footer logic
                'drawCallback' => 'function () {
                    let json = this.api().ajax.json();
                    $("#total_qty").html(json.total_qty);
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
                'data' => 'product.name',
                'name' => 'product.name',
                'title' => 'Product',
                'defaultContent' => '',
            ]),
            Column::make([
                'data' => 'sum_qty',
                'name' => 'sum_qty',
                'title' => 'Quantity',
                'class' => 'text-end',
                'footer' => '<strong id="total_qty"></strong>',
            ]),
            Column::make([
                'data' => 'sum_amount',
                'name' => 'sum_amount',
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
        return 'product_wise_sales_summary_' . now()->format('d_m_Y_h_i_s_A');
    }
}
