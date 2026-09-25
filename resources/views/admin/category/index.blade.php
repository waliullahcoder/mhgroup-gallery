@extends('layouts.admin.app')

@push('plugin')
    <link href="{{ asset('backend/css/TreeMenu.css') }}" rel="stylesheet" />
@endpush

@section('content')

    {{-- ADD ROOT CATEGORY --}}
    <a href="javascript:void(0)"
       class="btn btn-sm btn-primary"
       id="addBtnModal"
       data-id="">
        Add Root Category
    </a>

    {{-- SEARCH --}}
    <div class="form-group my-2">
        <input type="text" id="category_search" class="form-control" placeholder="Search">
    </div>

    {{-- CATEGORY TREE --}}
   <div class="tree">
    <ul id="makeTree">

        <li class="item">
            <h6 class="mb-0 text-uppercase fw-bold">Categories</h6>

            @foreach ($categories as $menu)
                {{-- PARENT CATEGORY --}}
                <li>
                    <a href="javascript:void(0)"
                       class="edit_category fw-bold"
                       data-url="{{ route('admin.category.edit', $menu->id) }}">
                        {{ $menu->name }} ({{$menu->position}})
                    </a>

                    <a style="padding-left:90%" href="{{ route('admin.category.ck.update', $menu->id) }}">Description</a>

                    {{-- CHILD CATEGORIES --}}
                    @if(isset($sub_categories[$menu->id]))
                        <ul>
                            @foreach ($sub_categories[$menu->id] as $submenu)
                                <li>
                                    <a href="javascript:void(0)"
                                       class="edit_category"
                                       data-url="{{ route('admin.category.edit', $submenu->id) }}">
                                        {{ $submenu->name }} ({{$submenu->position}})
                                    </a> 
                                    <a style="padding-left:90%" href="{{ route('admin.category.ck.update', $submenu->id) }}">Description</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach

        </li>

    </ul>
</div>


    {{-- CREATE MODAL --}}
    @include('admin.category.partial.create')

    {{-- EDIT MODAL CONTAINER --}}
    <div id="editForm"></div>

@endsection

@push('js')
<script src="{{ asset('backend/js/TreeMenu.js') }}"></script>

<script>
$(document).ready(function () {

    /* TREE MENU */
    make_tree_menu('makeTree');

    /* SEARCH */
    $('#category_search').on('keyup', function () {
        let query = $(this).val().toLowerCase();

        if (!query) {
            $('#makeTree li').show();
            return;
        }

        $('#makeTree li').hide();

        $('#makeTree li').filter(function () {
            return $(this).text().toLowerCase().indexOf(query) !== -1;
        }).each(function () {
            $(this).show().parents('li').show();
        });
    });

    /* CREATE ROOT CATEGORY */
    $(document).on('click', '#addBtnModal', function () {
        $('#parent_id').val('').trigger('change');
        $('#addModal').modal('show');
    });

    /* CREATE CHILD CATEGORY */
    $(document).on('click', '.add_child_category', function () {
        let parent_id = $(this).data('id');
        $('#parent_id').val(parent_id).trigger('change');
        $('#addModal').modal('show');
    });

    /* ==========================
        EDIT CATEGORY (RESOURCE)
       ========================== */
    $(document).on('click', '.edit_category', function () {

    let url = $(this).data('url');
    $('#editForm').html('');

    $.ajax({
        url: url,
        type: 'GET',
        data: {
            edit: true
        },
        success: function (res) {
            if (res.status === 'success') {
                $('#editForm').html(res.data);
                $('#editModal').modal('show');
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            alert('Edit load failed: ' + xhr.status);
        }
    });
});


    /* CLEAR MODAL AFTER CLOSE */
    $(document).on('hidden.bs.modal', '#editModal', function () {
        $('#editForm').html('');
    });

});
</script>
@endpush
