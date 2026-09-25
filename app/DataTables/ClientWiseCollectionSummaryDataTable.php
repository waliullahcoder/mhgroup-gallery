<?php

namespace App\DataTables;

use App\Models\Collection;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ClientWiseCollectionSummaryDataTable extends DataTable
{
    protected float $totalAmount = 0;

    /**
     * Get the query source of dataTable.
     */
    public function query(Request $request, Collection $model): QueryBuilder
    {
        $query = $model->with('client');

        if ($request->client_id) {
            $query->whereIn('client_id', (array) $request->client_id);
        }

        $dateRange = explode('to', $request->date_range ?? '');

        $startDate = $request->date_range
            ? date('Y-m-d', strtotime(trim($dateRange[0])))
            : date('Y-m-01');

        $endDate = $request->date_range
            ? date('Y-m-d', strtotime(trim($dateRange[1])))
            : date('Y-m-t');

        $query->whereBetween('date', [$startDate, $endDate])
            ->whereNotIn('collection_type', ['Return', 'Adjust'])
            ->groupBy('client_id')
            ->select([
                'client_id',
                DB::raw('SUM(amount) as sum_amount'),
            ]);

        // 🔥 calculate total BEFORE pagination (sum of grouped rows)
        $this->totalAmount = (clone $query)->get()->sum('sum_amount');

        return $query;
    }

    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()

            ->editColumn('sum_amount', function ($row) {
                return number_format($row->sum_amount, 2);
            })

            // 🔥 send fixed total to frontend
            ->with([
                'total_amount' => number_format($this->totalAmount, 2),
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

                // ✅ correct footer logic
                'drawCallback' => 'function () {
                    let json = this.api().ajax.json();
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
            Column::make([
                'data' => 'DT_RowIndex',
                'name' => 'DT_RowIndex',
                'title' => 'SL#',
                'orderable' => false,
                'searchable' => false,
                'class' => 'text-center',
                'width'     => '20',
            ]),
            Column::make([
                'data' => 'client.name',
                'name' => 'client.name',
                'title' => 'Client',
                'defaultContent' => '',
            ]),
            Column::make([
                'data' => 'sum_amount',
                'name' => 'sum_amount',
                'title' => 'Amount',
                'class' => 'text-end',
                'footer' => '<strong id="total_amount"></strong>',
            ]),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'client_wise_collection_summary_' . now()->format('d_m_Y_h_i_s_A');
    }
}
