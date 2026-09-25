<?php

namespace App\DataTables;

use App\Models\SalesReturnList;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductWiseSalesReturnHistoryDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('qty', fn($row) => number_format($row->qty))
            ->editColumn('amount', fn($row) => number_format($row->amount, 2))
            ->with([
                // ✅ FIXED: fully qualified column
                'total_amount' => number_format(
                    $query->sum('sales_return_lists.amount'),
                    2
                ),
            ]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Request $request, SalesReturnList $model): QueryBuilder
    {
        $start_date = now()->startOfMonth()->toDateString();
        $end_date   = now()->endOfMonth()->toDateString();

        if ($request->filled('date_range')) {
            [$start, $end] = array_map('trim', explode('to', $request->date_range));
            $start_date = date('Y-m-d', strtotime($start));
            $end_date   = date('Y-m-d', strtotime($end));
        }

        $query = $model
            ->select('sales_return_lists.*')
            ->with([
                'return',
                'store',
                'client',
                'product',
            ])
            ->leftJoin(
                'sales_returns',
                'sales_returns.id',
                '=',
                'sales_return_lists.sales_return_id'
            );

        // 🔹 filter by return info
        $query->whereHas('return', function ($q) use ($request, $start_date, $end_date) {

            if ($request->filled('store_id')) {
                $q->where('store_id', $request->store_id);
            }

            if ($request->filled('client_id')) {
                $q->whereIn('client_id', (array) $request->client_id);
            }

            $q->whereBetween('date', [$start_date, $end_date]);
        });

        if ($request->filled('product_id')) {
            $query->whereIn('product_id', (array) $request->product_id);
        }

        return $query
            ->orderByDesc('sales_returns.date')
            ->orderByDesc('sales_return_lists.id');
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
                'responsive' => true,
                'buttons' => [
                    Button::make('reload'),
                    Button::make('excel')->text('<i class="fal fa-file-spreadsheet"></i> Excel'),
                    [
                        'text' => '<i class="fal fa-file-pdf"></i> Print',
                        'className' => 'getPdf',
                    ],
                ],
                'drawCallback' => 'function () {
                    let json = this.api().ajax.json();
                    $("#total_amount").html(json.total_amount ?? "0.00");
                }',
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('SL#')
                ->orderable(false)
                ->searchable(false)
                ->width(10)
                ->addClass('text-center'),

            Column::make('return.formattedDate')
                ->title('Date')
                ->orderable(false)
                ->searchable(false)
                ->defaultContent('')
                ->addClass('text-nowrap'),

            Column::make('return.return_no')
                ->title('Return No')
                ->defaultContent('')
                ->addClass('text-nowrap'),

            Column::make('store.name')
                ->title('Store')
                ->defaultContent('')
                ->addClass('text-nowrap'),

            Column::make('client.name')
                ->title('Client')
                ->defaultContent('')
                ->addClass('text-nowrap'),

            Column::make('product.name')
                ->title('Product')
                ->defaultContent('')
                ->addClass('text-nowrap'),

            Column::make('qty')
                ->title('Quantity')
                ->addClass('text-end'),

            Column::make('amount')
                ->title('Amount')
                ->addClass('text-end')
                ->footer('<span id="total_amount"></span>'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'product_wise_sales_return_history_' . now()->format('d_m_Y_h_i_s_A');
    }
}
