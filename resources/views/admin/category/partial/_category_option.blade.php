@if (!in_array($item->id, $excludeIds))
    <option value="{{ $item->id }}" {{ $data->parent_id == $item->id ? 'selected' : '' }}>
        {{ $item->name }}
    </option>

    @if ($item->children->count())
        @foreach ($item->children as $child)
            @include('admin.category.partial._category_option', [
                'item' => $child,
                'data' => $data,
                'excludeIds' => $excludeIds, // pass it down
            ])
        @endforeach
    @endif
@endif
