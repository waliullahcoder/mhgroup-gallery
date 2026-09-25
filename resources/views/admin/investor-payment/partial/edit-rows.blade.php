@foreach ($profits as $item)
    @php
        if (!is_null(@$data)) {
            $old_data = @$data->list->where('distribution_list_id', $item->id)->first();
        }
    @endphp
    <tr id="profit_{{ $item->id }}">
        <td class="text-center" style="padding: 0.25rem 0.25rem;">{{ $loop->iteration }}</td>
        <td class="text-nowrap" style="padding: 4px 0.25rem;">{{ $item->profitDistribution->serial_no ?? '' }}</td>
        <td style="padding: 4px 0.25rem;">
            <input type="number" class="form-control input-sm text-end" step="any" value="{{ $item->amount }}"
                placeholder="0.00" readonly>
        </td>
        <td style="padding: 4px 0.25rem;">
            <input type="number" class="form-control input-sm text-end" step="any"
                value="{{ $item->paid_amount - ($old_data->amount ?? 0) }}" placeholder="0.00" readonly>
        </td>
        @if ($payment_type == 'Adjust')
            @php
                $checked = true;

                // calculate total payable for this item
                $payable = $item->amount - $item->paid_amount + ($old_data->amount ?? 0);

                if ($advance <= 0) {
                    $checked = false;
                    $paid = 0;
                    $due = $payable;
                } else {
                    // we can pay up to the payable amount, but not more than advance
                    $paid = min($advance, $payable);
                    $due = $payable - $paid;
                }

                // deduct used advance
                $advance -= $paid;
            @endphp

            <td style="padding: 4px 0.25rem;">
                <input type="number" class="form-control input-sm text-end amount" step="any"
                    id="amount_{{ $item->id }}" data-id="{{ $item->id }}" name="amount[{{ $item->id }}]"
                    max="{{ $payable }}" min="0" value="{{ $paid }}" placeholder="0.00">
            </td>
            <td style="padding: 4px 0.25rem;">
                <input type="number" class="form-control input-sm text-end due" step="any"
                    id="due_{{ $item->id }}" name="due[{{ $item->id }}]" value="{{ $due }}" readonly
                    placeholder="0.00">
            </td>
            <td style="padding: 4px 0.25rem;" class="text-center">
                <div class="custom-control custom-checkbox mx-auto ps-3">
                    <input type="checkbox" class="custom-control-input distribution_list_id"
                        id="distribution_list_id_{{ $item->id }}" name="distribution_list_id[]"
                        data-payable="{{ $payable }}" value="{{ $item->id }}"
                        {{ $checked ? 'checked' : '' }}>
                    <label for="distribution_list_id_{{ $item->id }}" class="custom-control-label"></label>
                </div>
            </td>
        @else
            <td style="padding: 4px 0.25rem;">
                <input type="number" class="form-control input-sm text-end amount" step="any"
                    id="amount_{{ $item->id }}" data-id="{{ $item->id }}" name="amount[{{ $item->id }}]"
                    max="{{ $item->amount - $item->paid_amount + ($old_data->amount ?? 0) }}" min="0"
                    value="{{ $old_data->amount ?? 0 }}" placeholder="0.00">
            </td>
            <td style="padding: 4px 0.25rem;">
                <input type="number" class="form-control input-sm text-end due" step="any"
                    id="due_{{ $item->id }}" name="due[{{ $item->id }}]"
                    value="{{ $item->amount - $item->paid_amount }}" readonly placeholder="0.00">
            </td>
            <td style="padding: 4px 0.25rem;" class="text-center">
                <div class="custom-control custom-checkbox mx-auto ps-3">
                    <input type="checkbox" class="custom-control-input distribution_list_id"
                        name="distribution_list_id[]"
                        data-payable="{{ $item->amount - $item->paid_amount + ($old_data->amount ?? 0) }}"
                        value="{{ $item->id }}" id="{{ $item->id }}"
                        {{ !is_null($old_data ?? null) ? 'checked' : '' }}>
                    <label for="{{ $item->id }}" class="custom-control-label"></label>
                </div>
            </td>
        @endif
    </tr>
@endforeach
