@if (count($combinations[0]) > 0)
    <table class="table table-bordered mb-0">
        <thead class="bg-primary text-white">
            <tr>
                <th class="text-center">Variant</th>
                <th class="text-center">Purchase Price</th>
                <th class="text-center">Retail Price</th>
                <th class="text-center">Photo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($combinations as $key => $combination)
                @php
                    $str = '';
                    foreach ($combination as $key => $item) {
                        $attribute_name = \App\Models\AttributeValue::where('id', $item)->first()->name;
                        if ($key > 0) {
                            $str .= '-';
                        }
                        $str .= str_replace(' ', '', $attribute_name);
                    }
                    $variant = $product->variants->where('variant', $str)->first();
                @endphp
                @if (strlen($str) > 0)
                    <tr>
                        <td>
                            <input type="text" name="variant_{{ $str }}"
                                value="{{ @$variant->variant ?? $str }}" class="form-control input-sm rounded-0" readonly>
                        </td>
                        <td>
                            <input type="number" name="purchase_price_{{ $str }}"
                                value="{{ $purchase_price ?? @$variant->purchase_price }}" min="0" step="0.01"
                                class="form-control input-sm rounded-0" required>
                        </td>
                        <td>
                            <input type="number" name="regular_price_{{ $str }}"
                                value="{{ $regular_price ?? @$variant->regular_price }}" min="0" step="0.01"
                                class="form-control input-sm rounded-0" required>
                        </td>
                        <td>
                            <input type="hidden" name="variant_id[]" value="{{ @$variant->id }}">
                            <input type="hidden" name="old_variant_img_{{ $str }}"
                                value="{{ @$variant->image }}">
                            <div class="d-flex align-items-center gap-2">
                                <input type="file" class="form-control input-sm rounded-0 flex-grow-1"
                                    name="variant_img_{{ $str }}" accept="image/*">
                                @if (file_exists(@$variant->image))
                                    <img class="flex-shrink-0" src="{{ asset(@$variant->image) }}" height="36"
                                        alt="Image">
                                @endif
                            </div>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
@endif
