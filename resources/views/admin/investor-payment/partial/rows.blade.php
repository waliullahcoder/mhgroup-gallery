@foreach ($profits as $item)
    <tr id="profit_{{ $item->id }}">
        <td class="text-center" style="padding: 0.25rem 0.25rem;">{{ $loop->iteration }}</td>
        <td class="text-nowrap" style="padding: 4px 0.25rem;">{{ $item->profitDistribution->serial_no ?? '' }}</td>
        <td style="padding: 4px 0.25rem;">
            <input type="number" class="form-control input-sm text-end" step="any" value="{{ $item->amount }}"
                placeholder="0.00" readonly>
        </td>
        <td style="padding: 4px 0.25rem;">
            <input type="number" class="form-control input-sm text-end" step="any" value="{{ $item->paid_amount }}"
                placeholder="0.00" readonly>
        </td>
        @if (@$type == 'Adjust')
            @php
                $checked = true;
                if ($advance <= 0) {
                    $checked = false;
                    $paid = 0;
                    $due = $item->amount - $item->paid_amount;
                } else {
                    $paid = $advance;
                    $due = $item->amount - $item->paid_amount - $advance;
                }
            @endphp
            <td style="padding: 4px 0.25rem;">
                <input type="number" class="form-control input-sm text-end amount" step="any"
                    id="amount_{{ $item->id }}" data-id="{{ $item->id }}" name="amount[{{ $item->id }}]"
                    max="{{ $item->amount - $item->paid_amount }}" min="0" value="{{ $paid }}"
                    placeholder="0.00">
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
                        data-payable="{{ $item->amount - $item->paid_amount }}" value="{{ $item->id }}"
                        id="{{ $item->id }}" {{ @$checked === true ? 'checked' : '' }}>
                    <label for="{{ $item->id }}" class="custom-control-label"></label>
                </div>
            </td>
        @else
            <td style="padding: 4px 0.25rem;">
                <input type="number" class="form-control input-sm text-end amount" step="any"
                    id="amount_{{ $item->id }}" data-id="{{ $item->id }}" name="amount[{{ $item->id }}]"
                    max="{{ $item->amount - $item->paid_amount }}" min="0" placeholder="0.00">
            </td>
            <td style="padding: 4px 0.25rem;">
                <input type="number" class="form-control input-sm text-end due" step="any"
                    id="due_{{ $item->id }}" name="due[{{ $item->id }}]"
                    value="{{ $item->amount - $item->paid_amount }}" readonly placeholder="0.00">
            </td>
            <td style="padding: 4px 0.25rem;" class="text-center">
                <div class="custom-control custom-checkbox mx-auto ps-3">
                    <input type="checkbox" class="custom-control-input distribution_list_id"
                        name="distribution_list_id[]" data-payable="{{ $item->amount - $item->paid_amount }}"
                        value="{{ $item->id }}" id="{{ $item->id }}">
                    <label for="{{ $item->id }}" class="custom-control-label"></label>
                </div>
            </td>
        @endif
    </tr>
@endforeach
