<li id="categoryItem{{ $category->id }}">
    <span class="category_name me-2">{{ $category->name }}</span>
    <a href="javascript:void(0);" class="add_child_category" data-id="{{ $category->id }}">[Add child category]</a>
    <a href="javascript:void(0);" class="edit_category mx-1"
        data-url="{{ Route('admin.category.edit', $category->id) }}">[Edit]</a>
    @if ($category->children->count() == 0)
        <a href="javascript:void(0);" class="delete_category"
            data-url="{{ Route('admin.category.destroy', $category->id) }}" data-id="{{ $category->id }}">[Delete]</a>
    @endif
    <div class="form-check form-switch switch-sm ms-2">
        <input class="form-check-input change-status c-pointer"
            data-url="{{ Route('admin.category.edit', $category->id) }}" type="checkbox" name="status"
            {{ $category->status == true ? 'checked' : '' }}>
    </div>

    @if ($category->children->count())
        <ul>
            @foreach ($category->children as $child)
                @include('admin.category.partial._category', ['category' => $child])
            @endforeach
        </ul>
    @endif
</li>
