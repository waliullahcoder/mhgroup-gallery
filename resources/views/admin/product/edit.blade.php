@extends('layouts.admin.edit_app')

@section('content')
    <nav class="nav__wrapper">
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-general-tab" data-bs-toggle="tab" data-bs-target="#nav-general"
                type="button" role="tab" aria-controls="nav-general" aria-selected="true">General</button>
            <button class="nav-link" id="nav-media-tab" data-bs-toggle="tab" data-bs-target="#nav-media" type="button"
                role="tab" aria-controls="nav-media" aria-selected="false">Files & Media</button>
            <button class="nav-link" id="nav-price-tab" data-bs-toggle="tab" data-bs-target="#nav-price" type="button"
                role="tab" aria-controls="nav-price" aria-selected="false">Price & Variation</button>
            <button class="nav-link" id="nav-publish-tab" data-bs-toggle="tab" data-bs-target="#nav-publish" type="button"
                role="tab" aria-controls="nav-publish" aria-selected="false">Publish</button>

            <button class="nav-link" id="nav-seo-tab" data-bs-toggle="tab" data-bs-target="#nav-seo" type="button"
                role="tab" aria-controls="nav-seo" aria-selected="false">SEO</button>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab"
            tabindex="0">
            <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">Product Information</h5>
            <div class="row g-3">
                <div class="col-sm-12">
                    <label for="name" class="form-label"><b>Product Name <span class="text-danger">*</span></b></label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="{{ old('name', $data->name) }}" placeholder="Name" required>
                </div>
                <input type="hidden"  name="code" value="{{ old('code', $data->code) }}">
               
                   @php
                        // Existing edition for this product (edit mode)
                        $existingEdition = App\Models\ProductEdition::where('product_id', $data->id)->first();
                    @endphp

                
                    
                
                
                <div class="col-12">
                    <label for="description" class="form-label"><b>Short Description</b></label>
                    <textarea class="form-control description" id="description" name="short_description" cols="30"
                        rows="10" placeholder="Short Description">{!! old('short_description', $data->short_description) !!}</textarea>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label"><b>Description</b></label>
                    <textarea class="form-control description" id="description" name="description" cols="30" rows="10"
                        placeholder="Description">{!! old('description', $data->description) !!}</textarea>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="nav-media" role="tabpanel" aria-labelledby="nav-media-tab" tabindex="0">
            <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">Product Files &amp; Media</h5>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label for="thumbnail" class="form-label"><b>Image</b></label>
                    <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                    @if (file_exists($data->thumbnail))
                        <img class="flex-shrink-0" src="{{ asset($data->thumbnail) }}" height="36" alt="Image">
                    @endif
                </div>
                <div class="col-sm-6">
                    <label for="images" class="form-label"><b>Other Images</b></label>
                    <input type="file" class="form-control" id="images" name="images[]" multiple
                        accept="image/*">
                    @foreach ($data->images as $item)
                        @if (file_exists($item->image))
                            <img class="flex-shrink-0" src="{{ asset($item->image) }}" height="36" alt="Image">
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="nav-price" role="tabpanel" aria-labelledby="nav-price-tab" tabindex="0">
            <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">Product price & Variation</h5>
            <div class="row g-3">
                <div class="col-12">
                    <div class="row g-3">
                        <label class="col-md-3 col-from-label" for="purchase_price"><b>Purchase price <span
                                    class="text-danger">*</span></b></label>
                        <div class="col-md-6">
                            <input type="number" min="0" step="0.01" placeholder="Purchase price"
                                name="purchase_price" id="purchase_price" class="form-control"
                                value="{{ old('purchase_price', $data->purchase_price) }}" required>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="row g-3">
                        <label class="col-md-3 col-from-label" for="regular_price"><b>Regular price <span
                                    class="text-danger">*</span></b></label>
                        <div class="col-md-6">
                            <input type="number" min="0" step="0.01" placeholder="Regular price"
                                name="regular_price" id="regular_price" class="form-control"
                                value="{{ old('regular_price', $data->regular_price) }}" required>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="row g-3">
                        <label class="col-sm-3 control-label" for="date_range"><b>Discount Date Range</b></label>
                        <div class="col-sm-6">
                            @php
                                $start_date = date('d-m-Y H:i:s', strtotime($data->discount_start_date));
                                $end_date = date('d-m-Y H:i:s', strtotime($data->discount_end_date));
                            @endphp
                            <input type="text" class="form-control date-range" name="date_range" id="date_range"
                                @if ($data->discount_start_date && $data->discount_end_date) value="{{ $start_date . ' to ' . $end_date }}" @endif
                                placeholder="Select Date" data-time-picker="true" data-format="DD-MM-Y HH:mm:ss"
                                data-separator=" to " autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="row g-3">
                        <label class="col-md-3 col-from-label" for="discount"><b>Discount <span
                                    class="text-danger">*</span></b></label>
                        <div class="col-md-3">
                            <input type="number" min="0" step="0.01" placeholder="Discount" name="discount"
                                id="discount" class="form-control" value="{{ old('discount', $data->discount) }}"
                                required>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control select" name="discount_type"
                                data-placeholder="Select Discount Type" required>
                                <option value="amount"
                                    {{ old('discount_type', $data->discount_type) == 'amount' ? 'selected' : '' }}>Flat
                                </option>
                                <option value="percent"
                                    {{ old('discount_type', $data->discount_type) == 'percent' ? 'selected' : '' }}>Percent
                                </option>
                            </select>
                        </div>

<!-- Size & Price -->
<div class="row g-3">
    <label class="col-md-3 col-form-label">
        <b>Size & Price</b>
    </label>
    <div class="col-md-9">
        <div id="sizePriceContainer">

            @php
                // existing variants; na thakle ekta khali row dekhabe
                $variants = $data->variants->count()
                    ? $data->variants
                    : collect([new \App\Models\ProductVariant()]);
            @endphp

            @foreach ($variants as $variant)
                <div class="row g-2 mb-2 size-price-row">

                    <!-- Variant ID (update er jonno dorkar) -->
                    <input type="hidden" name="variant_id[]" value="{{ $variant->id }}">

                    <!-- UOM -->
                    <div class="col-md-4">
                        <select class="form-select uom-select" name="uom_id[]" required>
                            <option value="" disabled {{ $variant->uom_id ? '' : 'selected' }}>
                                Select Unit
                            </option>

                            @foreach ($additionalData['uoms'] as $uom)
                                <option value="{{ $uom->id }}"
                                        data-uom-name="{{ $uom->name }}"
                                        {{ $data->uom_id == $uom->id ? 'selected' : '' }}>
                                    {{ $uom->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Size -->
                    <div class="col-md-3">
                        <input type="text"
                               name="size[]"
                               class="form-control size-input"
                               placeholder="Size"
                               value="{{ $variant->variant }}"
                               required>
                    </div>

                    <!-- Price -->
                    <div class="col-md-3">
                        <input type="number"
                               name="price[]"
                               class="form-control"
                               min="0"
                               step="0.01"
                               placeholder="Price"
                               value="{{ $variant->regular_price }}"
                               required>
                    </div>

                    <!-- Add / Remove -->
                    <div class="col-md-2">
                        @if ($loop->first)
                            <button type="button" class="btn btn-success add-more">
                                <i class="fa fa-plus"></i> Add More
                            </button>
                        @else
                            <button type="button" class="btn btn-danger remove-row">
                                <i class="fa fa-trash"></i>
                            </button>
                        @endif
                    </div>

                </div>
            @endforeach

        </div>
    </div>
</div>








                    </div>
                </div>

                

            </div>
        </div>
         <div class="tab-pane fade" id="nav-publish" role="tabpanel" aria-labelledby="nav-publish-tab" tabindex="0">
            <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                Product Publish
            </h5>

            <div class="row g-3">
                <div class="col-sm-12">
                    <label class="form-label">
                        <b>Categories <span class="text-danger">*</span></b>
                    </label>

                    <select class="form-select select2" 
                            name="category_ids[]" 
                            multiple 
                            data-placeholder="Select Categories" 
                            required
                            style="width:100%;height:400px;">

                        @foreach ($additionalData['categories'] as $item)
                            <option value="{{ $item->id }}"
                                {{ isset($data) && $data->categories->pluck('id')->contains($item->id) ? 'selected' : '' }}>
                               [ID:{{ $item->id }}] {{ $item->name }}
                            </option>
                        @endforeach

                    </select>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="nav-seo" role="tabpanel" aria-labelledby="nav-seo-tab" tabindex="0">
            <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">SEO Meta Tags</h5>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label for="meta_title" class="form-label"><b>Meta Title</b></label>
                    <input type="text" class="form-control" id="meta_title" name="meta_title"
                        value="{{ old('meta_title', $data->meta_title) }}" placeholder="Meta Title">
                </div>
                <div class="col-sm-6">
                    <label for="meta_image" class="form-label"><b>Meta Image</b></label>
                    <input type="file" class="form-control" id="meta_image" name="meta_image" accept="image/*">
                </div>
                <div class="col-12">
                    <label for="meta_description" class="form-label"><b>Meta Description</b></label>
                    <textarea class="form-control" name="meta_description" id="meta_description" cols="30" rows="5"
                        placeholder="Meta Description">{{ old('meta_description', $data->meta_description) }}</textarea>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            var input = document.querySelector('#tags');
            new Tagify(input);

            $(document).on('change', '#custom_barcode', function() {
                if ($(this).is(':checked')) {
                    $('#barcodeWrapper').show(); // show barcode input
                    $('#barcode').prop('required', true);
                } else {
                    $('#barcodeWrapper').hide(); // hide barcode input
                    $('#barcode').prop('required', false);
                }
            });

            updateSku();

            function add_more_customer_choice_option(i, name) {
                $.ajax({
                    url: "{{ url()->full() }}",
                    type: 'POST',
                    data: {
                        _method: 'GET',
                        attribute_id: i,
                        get_choices: true
                    },
                    success: function(data) {
                        var obj = JSON.parse(data);
                        $('#customer_choice_options').append(`
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <input type="hidden" name="choice_no[]" value="${i}">
                                        <input type="text" class="form-control" name="choice[]" value="${name}" placeholder="Choice Title" readonly>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control select attribute_choice" name="choice_options_${i}[]" multiple data-placeholder="Select ${name}">${obj}</select>
                                    </div>
                                </div>
                            </div>`);

                        $('.select').select2({
                            allowClear: true,
                        });
                    }
                });
            }

            $(document).on("change", ".attribute_choice", function() {
                updateSku();
            });

            $('input[name="purchase_price"], input[name="regular_price"]').on('keyup', function() {
                updateSku();
            });

            function updateSku() {
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.product.sku-combination.edit', $data->id) }}",
                    data: $('#update_form').serialize() + '&_method=POST',
                    success: function(response) {
                        $('#sku_combination').html(response);
                        $('#show-hide-div').toggle(response.length <= 1);
                    }
                });
            }

            $('#choice_attributes').on('change', function() {
                $('#customer_choice_options').html(null);
                $.each($("#choice_attributes option:selected"), function() {
                    add_more_customer_choice_option($(this).val(), $(this).text());
                });
                updateSku();
            });
        });



  // UOM change Body

    const container = document.getElementById('sizePriceContainer');

    container.addEventListener('change', function (e) {

        if (e.target.classList.contains('uom-select')) {

            const select = e.target;

            const selectedOption =
                select.options[select.selectedIndex];

            const uomName =
                selectedOption.getAttribute('data-uom-name');

            const row =
                select.closest('.size-price-row');

            const sizeInput =
                row.querySelector('.size-input');

            if (uomName) {
                sizeInput.placeholder = 'Enter ' + uomName;
            } else {
                sizeInput.placeholder = 'Size';
            }
        }

    });




        
    // Add More / Remove
    container.addEventListener('click', function (e) {

        // Add More
        if (e.target.closest('.add-more')) {

            const row = document.createElement('div');

            row.className = 'row g-2 mb-2 size-price-row';

            row.innerHTML = `
                <div class="col-md-4">
                    <select class="form-select uom-select"
                            name="uom_id[]"
                            required>

                        <option value="" selected disabled>
                            Select Unit
                        </option>

                        @foreach ($additionalData['uoms'] as $item)
                            <option value="{{ $item->id }}"
                                    data-uom-name="{{ $item->name }}">
                                {{ $item->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="col-md-3">
                    <input type="text"
                           name="size[]"
                           class="form-control size-input"
                           placeholder="Size"
                           required>
                </div>

                <div class="col-md-3">
                    <input type="number"
                           name="price[]"
                           class="form-control"
                           min="0"
                           step="0.01"
                           placeholder="Price"
                           required>
                </div>

                <div class="col-md-2">
                    <button type="button"
                            class="btn btn-danger remove-row">
                        <i class="fa fa-trash"></i> Remove
                    </button>
                </div>
            `;

            container.appendChild(row);
        }


        // Remove
        if (e.target.closest('.remove-row')) {

            const row =
                e.target.closest('.size-price-row');

            row.remove();
        }

    });
    </script>
@endpush
