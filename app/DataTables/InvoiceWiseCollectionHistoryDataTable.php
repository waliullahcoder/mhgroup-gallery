<?php

namespace App\DataTables;

use App\Models\Collection;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InvoiceWiseCollectionHistoryDataTable extends DataTable
{
    /**
     * Get the query source of dataTable.
     */
    public function query(Request $request, Collection $model): QueryBuilder
    {
        $query = $model->with(['client']);
        if ($request->client_id) {
            $query->whereIn('client_id', $request->client_id);
        }
        $date_range = explode('to', request('date_range'));
        $start_date = !is_null(request('date_range')) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null(request('date_range')) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');
        $query->where('date', '>=', $start_date)->where('date', '<=', $end_date);

        return $query->whereNotIn('collection_type', ['Return', 'Adjust'])->orderBy('date', 'desc');
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('total_amount', function () use ($query) {
                return number_format($query->sum('amount'), 2);
            });
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
                        total_amount = item.total_amount;
                    });
                    $("#total_amount").html(total_amount);
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
                'data'      => 'payment_no',
                'name'      => 'payment_no',
                'title'     => 'Collection No',
                'class'     => 'text-nowrap',
            ]),
            Column::make([
                'data'      => 'formattedDate',
                'name'      => 'formattedDate',
                'title'     => 'Date',
                'class'     => 'text-nowrap',
                'orderable' => false,
                'searchable' => false,
            ]),
            Column::make([
                'data'      => 'client.name',
                'name'      => 'client.name',
                'title'     => 'Client',
                'class'     => 'text-nowrap',
                'defaultContent' => ''
            ]),
            Column::make([
                'data'      => 'collection_type',
                'name'      => 'collection_type',
                'title'     => 'Pay Mode',
            ]),
            Column::make([
                'data'      => 'remarks',
                'name'      => 'remarks',
                'title'     => 'Remarks',
            ]),
            Column::make([
                'data'      => 'amount',
                'name'      => 'amount',
                'title'     => 'Amount',
                'class'     => 'text-nowrap text-end',
                'footer'    => '<span id="total_amount"></span>',
            ]),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'invoice_wise_payment_history_' . date('d_m_Y_h_i_s_A');
    }
}
