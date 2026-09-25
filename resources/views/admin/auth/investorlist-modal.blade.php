<div class="modal-header">
    <h1 class="modal-title fs-5" id="detailsModalLabel">Investors</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    @if (count($invests))
        <div class="table-responsive">
            <table class="table table-bordered table-striped target-table align-middle mb-0">
                <thead class="bg-primary border-primary text-white text-nowrap">
                    <tr>
                        <th class="py-1 text-center" width="30">SL#</th>
                        <th class="py-1">Investor</th>
                        <th class="py-1 text-end" width="200">Qty</th>
                        <th class="py-1 text-end" width="200">Amount</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    @foreach ($invests as $item)
                        <tr>
                            <td class="py-1 text-center serial">{{ $loop->iteration }}</td>
                            <td class="py-1 px-3 text-nowrap">
                                <b class="head_name">{{ $item->investor->name ?? '' }}</b>
                            </td>
                            <td class="py-1">
                                <input type="text" class="text-end form-control input-sm"
                                    value="{{ number_format($item->sumQty) }}" readonly>
                            </td>
                            <td class="py-1">
                                <input type="text" class="text-end form-control input-sm"
                                    value="{{ number_format($item->sumAmount) }}" readonly>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-primary border-primary">
                    <tr>
                        <th colspan="2" class="py-1 text-end text-white">Total Amount</th>
                        <td class="py-1">
                            <input type="text" step="any" class="text-end form-control input-sm"
                                value="{{ number_format($invests->sum('sumQty')) }}" readonly>
                        </td>
                        <td class="py-1">
                            <input type="text" step="any" class="text-end form-control input-sm"
                                value="{{ number_format($invests->sum('sumAmount')) }}" readonly>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>
