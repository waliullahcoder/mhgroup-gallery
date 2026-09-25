@extends('layouts.admin.create_app')

@section('content')
    <div class="row g-3">
        <div class="col-sm-6">
            <label for="title" class="form-label"><b>Section Title <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
        </div>
        <div class="col-sm-6">
            <label for="type" class="form-label"><b>Section Type <span class="text-danger">*</span></b></label>
            <select name="type" id="type" class="form-select select" data-placeholder="Select Section Type"
                required>
                <option value="Category Product">Category Product</option>
                <option value="Trending Product">Trending Product</option>
                <option value="New Product">New Product</option>
                <option value="Featured Category">Featured Category</option>
                <option value="Category Carousel">Category Carousel</option>
                <option value="Popular Writter">Popular Writter</option>
                <option value="Banner">Banner</option>
                <option value="Brand">Brand</option>
            </select>
        </div>
        <div class="col-12" id="responseWrapper">
            <div class="row g-3">
                <div class="col-sm-6" id="categoryArea">
                    <label for="category_id" class="form-label"><b>Category <span class="text-danger">*</span></b></label>
                    <select name="category_id" id="category_id" class="form-select select"
                        data-placeholder="Select Category" required>
                        <option value=""></option>
                        @php
                            $categories = \App\Models\Category::root()
                                ->with(['children'])
                                ->orderBy('name', 'asc')
                                ->get();
                        @endphp
                        <option value="">-- Select Category --</option>
                        @foreach ($categories as $category)
                            @include('admin.home-section._category', [
                                'category' => $category,
                                'prefix' => '',
                            ])
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('change', '#type', function() {
                var type = $(this).val();
                $('#responseWrapper').html('');

                if (type) {
                    $.ajax({
                        url: '{{ request()->fullUrl() }}',
                        method: 'POST',
                        data: {
                            _method: 'GET',
                            type: type
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                $('#responseWrapper').html(response.data);
                                $('.select').select2({
                                    allowClear: true,
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('Error updating order:', xhr.responseText);
                        }
                    });
                }
            });

            $(document).on('click', '.add-extra-attachement', function() {
                $('#fileUploadsContainer').append(
                    `<div class="row g-2 pt-2">
                        <div class="col-sm-6">
                            <div class="file-input form-control">
                                <input type="file" name="banner[]" class="form-control" accept="image/*">
                                <span class="file-input-button btn btn-default">
                                    Select File
                                </span>
                                <span class="file-input-text">No file selected</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" name="link[]" class="form-control" placeholder="Link">
                        </div>
                    </div>`
                );
            });

            $(document).on('click', '.file-input-remove', function() {
                $(this).closest('.file-input').remove();
            });

            $(document).on('change', 'input[type="file"]', function(event) {
                const file = event.target.files[0];
                if (file) {
                    $(this).closest('.file-input').find('.file-input-text').text(file.name);
                } else {
                    $(this).closest('.file-input').find('.file-input-text').text('No file selected');
                }
            });
        });
    </script>
@endpush
