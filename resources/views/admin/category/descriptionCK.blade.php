@extends('layouts.admin.app')

@section('content')
<form action="{{ route('admin.category.ck.update', $data->id) }}"
      method="POST"
      enctype="multipart/form-data">
    @csrf
    @method('PUT')
      <div class="row">
        <div class="col-md-12 col-sm-12">
                <div class="modal-header">
                    <h5 class="head-title">Update Description for {{$data->name}}</h5>
                    <a href="{{ route('admin.category.index') }}" class="btn-close" data-bs-dismiss="modal"></a>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                    <textarea class="form-control description" id="description" name="description" placeholder="Description">{!! old('description', $data->description) !!}</textarea>

                    </div>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('admin.category.index') }}"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">Close</a>
                    <button type="submit"
                            class="btn btn-primary">Update</button>
                </div>

            </div>
        </div>
    
</form>
@endsection
