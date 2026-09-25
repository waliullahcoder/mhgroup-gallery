<?php

namespace App\DataTables;

use App\Models\SalesReturn;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InvoiceWiseSalesReturnHistoryDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()

            ->addColumn('products', function ($row) {
                $html = '<ul class="mb-0">';
                foreach ($row->list as $item) {
                    $html .= '<li class="text-nowrap">'
                        . optional($item->product)->name
                        . ' <span class="text-dark"> x '
                        . number_format($item->qty)
                        . ' Pcs</span></li>';
                }
                return $html . '</ul>';
            })

            ->editColumn('amount', function ($row) {
                return number_format($row->amount, 2);
            })

            ->rawColumns(['products'])

            // 🔹 Send extra data to JS (footer total)
            ->with([
                'total_amount' => number_format($query->sum('amount'), 2),
            ]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Request $request, SalesReturn $model): QueryBuilder
    {
        $start_date = now()->startOfMonth()->toDateString();
        $end_date   = now()->endOfMonth()->toDateString();

        if ($request->filled('date_range')) {
            [$start, $end] = array_map('trim', explode('to', $request->date_range));
            $start_date = date('Y-m-d', strtotime($start));
            $end_date   = date('Y-m-d', strtotime($end));
        }

        $query = $model->with([
            'store',
            'client',
            'list.product'
        ]);

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('client_id')) {
            $query->whereIn('client_id', (array) $request->client_id);
        }

        if ($request->filled('product_id')) {
            $query->whereHas('list', function ($q) use ($request) {
                $q->whereIn('product_id', (array) $request->product_id);
            });
        }

        return $query
            ->whereBetween('date', [$start_date, $end_date])
            ->orderByDesc('date');
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
                }'
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

            Column::make('return_no')
                ->title('Return No')
                ->addClass('text-nowrap'),

            Column::make('date')
                ->title('Date')
                ->addClass('text-nowrap'),

            Column::make('client.name')
                ->title('Client')
                ->defaultContent('')
                ->addClass('text-nowrap'),

            Column::make('store.name')
                ->title('Store')
                ->defaultContent('')
                ->addClass('text-nowrap'),

            Column::computed('products')
                ->title('Products')
                ->orderable(false)
                ->searchable(false),

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
        return 'invoice_wise_sales_return_' . now()->format('d_m_Y_h_i_s_A');
    }
}
