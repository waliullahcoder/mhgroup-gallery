@extends('layouts.admin.app')

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <form action="{{ route('admin.role-permission.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-header pe-2 py-2 d-flex flex-row justify-content-between align-items-center">
                        <h6 class="h6 mb-0 flex-grow-1 text-uppercase">Setup Permissions</h6>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.role.index') }}" class="btn btn-primary btn-sm">Go Back</a>
                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="custom-control custom-checkbox d-flex gap-2">
                                    <input type="checkbox" id="checkAll" class="custom-control-input">
                                    <label class="form-label custom-control-label c-pointer mb-0 ps-2"
                                        for="checkAll"><b>Check All</b></label>
                                </div>
                            </div>

                            <div class="col-12 custom-accordion">
                                <div class="accordion" id="roleAccordionParentGroup">
                                    @foreach ($root_menus as $root_menu)
                                        @can($root_menu->permission->name)
                                            @include('admin.role.partials.menu_item', [
                                                'menu' => $root_menu,
                                                'permission_id' => $permission_id,
                                            ])
                                        @endcan
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-end pe-2 py-2">
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#checkAll').on('click', function() {
                $('input[type=checkbox]').prop('checked', this.checked);
            });

            $('.check_group').on('change', function() {
                const checked = this.checked;
                $(this).closest('.accordion-item').find('input[type=checkbox]').prop('checked', checked);
            });
        });
    </script>
@endpush
