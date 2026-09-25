@foreach ($sales as $item)
    @php
        if (!is_null(@$data)) {
            $old_data = @$data->list->where('sales_id', $item->id)->first();
        }
    @endphp
    <tr id="sales_{{ $item->id }}">
        <td class="text-center" style="padding: 0.25rem 0.25rem;">{{ $loop->iteration }}</td>
        <td class="text-nowrap" style="padding: 4px 0.25rem;">{{ @$item->invoice }}</td>
        <td style="padding: 4px 0.25rem;">
            <input type="number" class="form-control input-sm text-end sales_amount" step="any"
                id="sales_amount_{{ $item->id }}" name="sales_amount[{{ $item->id }}]"
                value="{{ $item->net_amount - $item->return_amount }}" placeholder="0.00" readonly>
        </td>
        <td style="padding: 4px 0.25rem;">
            <input type="number" class="form-control input-sm text-end previous_paid" step="any"
                id="previous_paid_{{ $item->id }}" name="previous_paid[{{ $item->id }}]"
                value="{{ $item->paid - ($old_data->amount ?? 0) }}" placeholder="0.00" readonly>
        </td>
        @if ($type == 'Adjust')
            @php
                $checked = true;

                // Total payable for this sales item
                $payable = $item->net_amount - $item->return_amount - $item->paid + ($old_data->amount ?? 0);

                if ($advance <= 0) {
                    $checked = false;
                    $paid = 0;
                    $due = $payable;
                } else {
                    // Pay as much as possible, but not more than payable
                    $paid = min($advance, $payable);
                    $due = $payable - $paid;
                }

                // Deduct used advance
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
                    <input type="checkbox" class="custom-control-input sales_id" id="sales_id_{{ $item->id }}"
                        name="sales_id[]" data-payable="{{ $payable }}" value="{{ $item->id }}"
                        {{ $checked ? 'checked' : '' }}>
                    <label for="sales_id_{{ $item->id }}" class="custom-control-label"></label>
                </div>
            </td>
        @else
            <td style="padding: 4px 0.25rem;">
                <input type="number" class="form-control input-sm text-end amount" step="any"
                    id="amount_{{ $item->id }}" data-id="{{ $item->id }}" name="amount[{{ $item->id }}]"
                    max="{{ $item->net_amount - $item->return_amount - $item->paid + ($old_data->amount ?? 0) }}"
                    min="0" value="{{ $old_data->amount ?? 0 }}" placeholder="0.00">
            </td>
            <td style="padding: 4px 0.25rem;">
                <input type="number" class="form-control input-sm text-end due" step="any"
                    id="due_{{ $item->id }}" name="due[{{ $item->id }}]"
                    value="{{ $item->net_amount - $item->return_amount - $item->paid }}" readonly placeholder="0.00">
            </td>
            <td style="padding: 4px 0.25rem;" class="text-center">
                <div class="custom-control custom-checkbox mx-auto ps-3">
                    <input type="checkbox" class="custom-control-input sales_id" id="sales_id_{{ $item->id }}"
                        name="sales_id[]"
                        data-payable="{{ $item->net_amount - $item->return_amount - $item->paid + ($old_data->amount ?? 0) }}"
                        value="{{ $item->id }}" {{ !is_null($old_data ?? null) ? 'checked' : '' }}>
                    <label for="sales_id_{{ $item->id }}" class="custom-control-label"></label>
                </div>
            </td>
        @endif
    </tr>
@endforeach
