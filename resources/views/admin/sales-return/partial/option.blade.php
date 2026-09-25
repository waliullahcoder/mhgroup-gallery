@foreach ($list as $item)
    @php
        $addition_qty = 0;

        if (!empty($data)) {
            $addition_qty = $data->list->where('sales_list_id', $item->id)->sum('qty');
        }

        $final_qty = $item->qty - $item->return_qty + $addition_qty;
        $final_amount = $item->net_amount - ($item->return_qty + $addition_qty) * $item->rate;
    @endphp

    <option value="{{ $item->id }}" data-id="{{ $item->sales_id }}" data-invoice="{{ $item->sales->invoice ?? '' }}"
        data-rate="{{ $item->rate }}" data-qty="{{ $final_qty }}" data-amount="{{ $final_amount }}"
        data-product="{{ $item->product_id }}" data-edition="{{ $item->product_edition_id }}">
        {{ $item->product->name ?? '' }} - {{ $item->edition->name ?? '' }}
    </option>
@endforeach
