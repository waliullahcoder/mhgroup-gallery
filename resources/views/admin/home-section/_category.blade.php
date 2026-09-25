<option value="{{ $category->id }}">
    {!! $prefix !!} {{ $category->name }}
</option>

@if ($category->children && $category->children->count())
    @foreach ($category->children as $child)
        @include('admin.home-section._category', ['category' => $child, 'prefix' => $prefix . '&nbsp; '])
    @endforeach
@endif
