@extends('layouts.admin.create_app')

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <label for="parent_id_first" class="form-label"><b>Root menu</b></label>
            <div class="custom-select">
                <select class="form-control select2 custom-select__element" name="parent_id" id="parent_id_first">
                    <option value="" selected>Select Root</option>
                    @foreach ($parent_menus as $key => $parent_menu)
                        <option value="{{ $parent_menu->id }}">{{ $parent_menu->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-12" id="append_wrapper" style="display: none;">

        </div>
        <div class="col-12">
            <label for="name" class="form-label require"><b>Menu Label</b></label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                placeholder="Menu Label" required>
        </div>
        <div class="col-12">
            <label for="icon" class="form-label"><b>Menu Icon</b></label>
            <input type="text" class="form-control" id="icon" name="icon" value="{{ old('icon') }}"
                placeholder="Menu Icon (Fontawesom 5.0)">
        </div>
        <div class="col-12">
            <label for="route" class="form-label require"><b>Route</b></label>
            <input type="text" class="form-control" id="route" name="route" value="{{ old('route') }}"
                placeholder="Route">
        </div>
        <div class="col-12">
            <label for="order" class="form-label require"><b>Serial</b></label>
            <input type="number" class="form-control" id="order" name="order" value="{{ old('order') }}"
                placeholder="Serial">
        </div>
        <div class="col-12">
            <label for="Status" class="form-label"><b>Status</b></label>
            <div class="custom-select">
                <select class="form-select select" name="status" id="status" required>
                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('change', '#parent_id_first', function(e) {
                e.preventDefault();
                let root_id = $(this).val();
                if (root_id != '') {
                    $.ajax({
                        url: '{{ request()->fullUrl() }}',
                        type: 'POST',
                        data: {
                            _method: 'GET',
                            root_id: root_id,
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                $('#append_wrapper > div').remove();
                                var html =
                                    `<div><label for="parent_id" class="form-label"><b>Parent menu</b></label>
                                        <div class="custom-select">
                                            <select class="form-control select2 custom-select__element" name="parent_id" id="parent_id">
                                                <option value="" selected>Select Parent</option>`;
                                $.each(response.parent_menus, function(key, value) {
                                    html +=
                                        `<option value="${value.id}">${value.name}</option>`;
                                });
                                html += `</select>
                                        </div>
                                    </div>`;
                                $('#append_wrapper').append(html);
                                $('#append_wrapper').show();
                            }
                        }
                    });
                } else {
                    $('#append_wrapper > div').remove();
                    $('#append_wrapper').hide();
                }
            });

            $(document).on('submit', '#store_form', function(e) {
                if ($('#parent_id').val() == '') {
                    $('#parent_id').val($('#parent_id_first').val());
                }
            });
        });
    </script>
@endpush
