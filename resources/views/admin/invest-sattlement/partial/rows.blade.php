@foreach ($invests as $item)
    <tr>
        <td class="text-center">{{ $loop->iteration }}</td>
        <td>{{ $item->invest_no }}</td>
        <td>{{ $item->formattedDate }}</td>
        <td style="padding: 4px 0.25rem;">
            <input type="number" class="form-control input-sm text-end" step="any" value="{{ $item->qty }}"
                placeholder="0.00" readonly>
        </td>
        <td style="padding: 4px 0.25rem;">
            <input type="number" class="form-control input-sm text-end" step="any" value="{{ $item->amount }}"
                placeholder="0.00" readonly>
        </td>
        <td style="padding: 4px 0.25rem;" class="text-center">
            <div class="custom-control custom-checkbox mx-auto ps-3">
                <input type="checkbox" class="custom-control-input invest_id" name="invest_id[]"
                    value="{{ $item->id }}" id="{{ $item->id }}">
                <label for="{{ $item->id }}" class="custom-control-label"></label>
            </div>
        </td>
    </tr>
@endforeach
