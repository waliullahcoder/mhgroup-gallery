@extends('layouts.admin.app')

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <form action="{{ Route('admin.admin-menu-action.update', $data->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-header d-flex flex-row justify-content-between align-items-center pe-2 py-2">
                        <h6 class="h6 mb-0 text-uppercase">Update Admin Menu Action</h6>
                        <div>
                            <a href="{{ Route('admin.admin-menu-action.index', $data->admin_menu_id) }}"
                                class="btn btn-primary btn-sm">Go Back</a>
                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name" class="form-label require"><b>Action Label</b></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ $data->name }}" placeholder="Action Label" autofocus required>
                            </div>
                            <div class="col-12">
                                <label for="route" class="form-label require"><b>Route</b></label>
                                <input type="text" class="form-control" id="route" name="route"
                                    value="{{ $data->route }}" placeholder="Route" required>
                            </div>
                            <div class="col-12">
                                <label for="Status" class="form-label"><b>Status</b></label>
                                <select class="form-control select" name="status" id="status"
                                    data-placeholder="Select Status" required>
                                    <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
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
