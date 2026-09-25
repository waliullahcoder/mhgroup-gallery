<?php

namespace App\DataTables;

use App\Models\Sales;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InvoiceWiseSalesHistoryDataTable extends DataTable
{
    protected float $totalAmount = 0;

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
                        . ' Pcs'
                        . '</span></li>';
                }
                return $html . '</ul>';
            })

            ->addColumn('net_amount', function ($row) {
                return number_format($row->net_amount, 2);
            })

            // 🔥 send total sum to frontend
            ->with([
                'total_amount' => number_format($this->totalAmount, 2),
            ])

            ->rawColumns(['products']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Request $request, Sales $model): QueryBuilder
    {
        $dateRange = explode('to', $request->date_range ?? '');

        $startDate = $request->date_range
            ? date('Y-m-d', strtotime(trim($dateRange[0])))
            : date('Y-m-01');

        $endDate = $request->date_range
            ? date('Y-m-d', strtotime(trim($dateRange[1])))
            : date('Y-m-t');

        $query = $model->with(['store', 'list.product', 'client'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($request->store_id) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->product_id) {
            $query->whereHas('list', function ($q) use ($request) {
                $q->whereIn('product_id', (array) $request->product_id);
            });
        }

        // 🔥 IMPORTANT: calculate total BEFORE pagination
        $this->totalAmount = (clone $query)->sum('net_amount');

        return $query->orderBy('date', 'desc');
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

                // ✅ fixed footer update
                'drawCallback' => 'function () {
                    let json = this.api().ajax.json();
                    $("#net_amount").html(json.total_amount);
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
                'data' => 'invoice',
                'name' => 'invoice',
                'title' => 'Invoice',
                'class' => 'text-nowrap',
            ]),
            Column::make([
                'data' => 'formattedDate',
                'name' => 'formattedDate',
                'title' => 'Date',
                'orderable' => false,
                'searchable' => false,
            ]),
            Column::make([
                'data' => 'client.name',
                'name' => 'client.name',
                'title' => 'Client',
                'defaultContent' => '',
            ]),
            Column::make([
                'data' => 'client.phone',
                'name' => 'client.phone',
                'title' => 'Phone',
                'defaultContent' => '',
            ]),
            Column::make([
                'data' => 'products',
                'name' => 'products',
                'title' => 'Products',
                'orderable' => false,
                'searchable' => false,
            ]),
            Column::make([
                'data' => 'net_amount',
                'name' => 'net_amount',
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
        return 'invoice_wise_sales_history_' . now()->format('d_m_Y_h_i_s_A');
    }
}
