<?php

namespace App\DataTables;

use App\Models\Coa;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CoaListDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<CoaList> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('parent_head', function ($row) {
                return ($row->head_type == 'A' ? 'Asset' : '') . ($row->head_type == 'L' ? 'Liabilities' : '') . ($row->head_type == 'I' ? 'Income' : '') . ($row->head_type == 'E' ? 'Expense' : '');
            });
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<CoaList>
     */
    public function query(Request $request, Coa $model): QueryBuilder
    {
        $query = $model->query();
        $parent_head = $request->parent_head;
        if ($parent_head) {
            $query->where('head_code', 'LIKE', $parent_head . '%')->where('transaction', 1);
        }
        return $query->orderBy('head_name', 'asc');
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
                'data'      => 'head_code',
                'name'      => 'head_code',
                'title'     => 'Head Code',
                'class'     => 'text-nowrap',
            ]),
            Column::make([
                'data'      => 'head_name',
                'name'      => 'head_name',
                'title'     => 'Head Name',
                'class'     => 'text-nowrap',
            ]),
            Column::make([
                'data'      => 'parent_head',
                'name'      => 'parent_head',
                'title'     => 'Head Type',
                'class'     => 'text-nowrap',
                'orderable' => false,
                'searchable' => false,
            ]),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'coa_list_' . date('d_m_Y_h_i_s_A');
    }
}
