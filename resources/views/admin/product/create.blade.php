@extends('layouts.admin.create_app')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
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
                <div class="col-sm-6">
                <label for="name" class="form-label"><b>Product Name <span class="text-danger">*</span></b></label>
                  <input type="text"
                        name="name"
                        value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="Product Name" required>

                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
               
                <div class="col-sm-6">
                    <label for="author_id" class="form-label"><b>Author <span class="text-danger">*</span></b></label>
                    <select class="form-select" name="author_id" id="author_id"
                        data-placeholder="Select Author" required>
                       
                        @foreach ($authors as $item)
                            <option value="{{ $item->id }}" {{ old('author_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                 <div class="col-sm-6">
                    <label for="publication_id" class="form-label"><b>Publication <span class="text-danger">*</span></b></label>
                    <select class="form-select" name="publication_id" id="publication_id"
                        data-placeholder="Select Publication" required>
                        @foreach ($publications as $item)
                            <option value="{{ $item->id }}" {{ old('publication_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label for="brand_id" class="form-label"><b>Brand</b><span class="text-danger">*</span></b></label>
                    <select class="form-select select" name="brand_id" id="brand_id" data-placeholder="Select Brand" required>
                        <option value=""></option>
                        @foreach ($brands as $item)
                            <option value="{{ $item->id }}" {{ old('brand_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
              
                <div class="col-6">
                    <label for="vendor_id" class="form-label"><b>Suppliers</b></label>
                    <select name="vendor_id[]" id="vendor_id" class="form-select select" data-placeholder="Select Vendors"
                        multiple>
                        <option value=""></option>
                        @foreach ($vendors as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label for="tags" class="form-label"><b>Tags</b></label>
                    <input type="text" class="form-control" id="tags" name="tags[]" value="{{ old('tags.0') }}"
                        placeholder="Tags">
                </div>
                <div class="col-sm-6">
                    <label for="edition_name" class="form-label"><b>Edition <span class="text-danger">*</span></b></label>
                    <select name="edition_name" id="edition_name"
                        class="form-control @error('edition_name') is-invalid @enderror" required>
                        @php
                            $editions = ['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh'];
                        @endphp
                        @foreach($editions as $edition)
                            <option value="{{ $edition }} Edition"
                                {{ old('edition_name') == $edition . ' Edition' ? 'selected' : '' }}>
                                {{ $edition }} Edition
                            </option>
                        @endforeach
                    </select>

                    @error('edition_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                 <div class="col-sm-6">
                    <label for="product_type" class="form-label"><b>Type <span class="text-danger">*</span></b></label>
                            <select class="form-control select" name="product_type"
                                data-placeholder="Select Product Type" required>
                                <option value="other" {{ old('product_type') == 'other' ? 'selected' : '' }}>Other</option>
                                <option value="fragrance" {{ old('product_type') == 'fragrance' ? 'selected' : '' }}>Fragrance</option>
                                <option value="mirror" {{ old('product_type') == 'mirror' ? 'selected' : '' }}>Mirror</option>
                                <option value="cloths" {{ old('product_type') == 'cloths' ? 'selected' : '' }}>Cloths</option>
                                <option value="frozen" {{ old('product_type') == 'frozen' ? 'selected' : '' }}>Frozen</option>
                                <option value="ornaments" {{ old('product_type') == 'ornaments' ? 'selected' : '' }}>Ornaments</option>
                                <option value="book" {{ old('product_type') == 'book' ? 'selected' : '' }}>Book
                                </option>
                            </select>
                        </div>
                <div class="col-12">
                   
                    <div class="row g-2">
                        <label for="favorite" class="col-sm-3 col-md-3 col-lg-2 control-label"><b>Is Favorite:</b></label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="favorite" name="favorite"
                                    {{ old('favorite') ? 'checked' : '' }}>
                                <label for="favorite" class="custom-control-label"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row g-2">
                        <label for="custom_barcode" class="col-sm-3 col-md-3 col-lg-2 control-label"><b>Is Custom
                                Barcode:</b></label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="custom_barcode"
                                    name="custom_barcode" {{ old('custom_barcode') ? 'checked' : '' }}>
                                <label for="custom_barcode" class="custom-control-label"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12" id="barcodeWrapper"
                    style="display: {{ old('custom_barcode') ? 'block' : 'none' }};">
                    <div class="row g-2">
                        <label for="barcode" class="col-sm-3 col-md-3 col-lg-2 control-label"><b>Custom
                                Barcode:</b></label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="text" class="form-control" id="barcode" name="barcode"
                                placeholder="Barcode" value="{{ old('barcode') }}"
                                {{ old('custom_barcode') ? 'required' : '' }}>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label"><b>Short Description</b></label>
                    <textarea class="form-control description" id="description" name="short_description" cols="30"
                        rows="10" placeholder="Short Description">{!! old('short_description') !!}</textarea>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label"><b>Description</b></label>
                    <textarea class="form-control description" id="description" name="description" cols="30" rows="10"
                        placeholder="Description">{!! old('description') !!}</textarea>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="nav-media" role="tabpanel" aria-labelledby="nav-media-tab" tabindex="0">
            <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">Product Files &amp; Media</h5>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label for="thumbnail" class="form-label"><b>Image</b></label>
                    <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                </div>
                <div class="col-sm-6">
                    <label for="images" class="form-label"><b>Other Images</b></label>
                    <input type="file" class="form-control" id="images" name="images[]" multiple
                        accept="image/*">
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
                            <input type="number" min="0" value="0" step="0.01"
                                placeholder="Purchase price" name="purchase_price" id="purchase_price"
                                class="form-control" value="{{ old('purchase_price') }}" required>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="row g-3">
                        <label class="col-md-3 col-from-label" for="regular_price"><b>Regular price <span
                                    class="text-danger">*</span></b></label>
                        <div class="col-md-6">
                            <input type="number" min="0" value="0" step="0.01"
                                placeholder="Regular price" name="regular_price" id="regular_price" class="form-control"
                                value="{{ old('regular_price') }}" required>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="row g-3">
                        <label class="col-sm-3 control-label" for="date_range"><b>Discount Date Range</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control date-range" name="date_range" id="date_range"
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
                            <input type="number" min="0" value="0" step="0.01" placeholder="Discount"
                                name="discount" id="discount" class="form-control" value="{{ old('discount') }}"
                                required>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control select" name="discount_type"
                                data-placeholder="Select Discount Type" required>
                                <option value="amount" {{ old('discount_type') == 'amount' ? 'selected' : '' }}>Flat
                                </option>
                                <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Percent
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

            <!-- First Row -->
            <div class="row g-2 mb-2 size-price-row">

                <!-- UOM -->
                <div class="col-md-4">
                    <select class="form-select uom-select"
                            name="uom_id[]"
                            required>

                        <option value="" selected disabled>
                            Select Unit
                        </option>

                        @foreach ($uoms as $item)
                            <option value="{{ $item->id }}"
                                    data-uom-name="{{ $item->name }}">
                                {{ $item->name }}
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
                           required>
                </div>

                <!-- Add More -->
                <div class="col-md-2">
                    <button type="button"
                            class="btn btn-success add-more">
                        <i class="fa fa-plus"></i> Add More
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>


                    </div>
                </div>

                <div class="col-12">
                    <div class="row g-3">
                        <label class="col-md-3 col-from-label" for="sku"><b>SKU</b></label>
                        <div class="col-md-6">
                            <input type="text" name="sku" id="sku" class="form-control" placeholder="SKU"
                                value="{{ old('sku') }}">
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" value="Attributes" disabled>
                        </div>
                        <div class="col-md-8">
                            <select name="choice_attributes[]" id="choice_attributes" class="form-control select"
                                data-selected-text-format="count" data-live-search="true" multiple
                                data-placeholder="Choose Attributes">
                                @foreach ($attributes as $attribute)
                                    <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <p>Choose the attributes of this product and then input values of each attribute</p>
                </div>

                <div class="col-12">
                    <div class="customer_choice_options row g-3" id="customer_choice_options">
                    </div>
                </div>

                <div class="sku_combination" id="sku_combination">

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

                        @foreach ($subcategories as $item)
                            <option value="{{ $item->id }}">
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
                        value="{{ old('meta_title') }}" placeholder="Meta Title">
                </div>
                <div class="col-sm-6">
                    <label for="meta_image" class="form-label"><b>Meta Image</b></label>
                    <input type="file" class="form-control" id="meta_image" name="meta_image" accept="image/*">
                </div>
                <div class="col-12">
                    <label for="meta_description" class="form-label"><b>Meta Description</b></label>
                    <textarea class="form-control" name="meta_description" id="meta_description" cols="30" rows="5"
                        placeholder="Meta Description">{{ old('meta_description') }}</textarea>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
{{-- <script>
 const subCategories = @json($sub_categories);
    document.getElementById('category_id').addEventListener('change', function () {
        const categoryId = this.value;
        const subCategorySelect = document.getElementById('sub_category_id');

        // reset
        subCategorySelect.innerHTML = '<option value="">-- Select Sub Category --</option>';

        if (categoryId && subCategories[categoryId]) {
            subCategories[categoryId].forEach(function (sub) {
                const option = document.createElement('option');
                option.value = sub.id;
                option.textContent = sub.name;
                subCategorySelect.appendChild(option);
            });
        }
    });
</script> --}}
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
                    $('#barcode').val(''); // optional: clear input when hiding
                    $('#barcode').prop('required', false);
                }
            });

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
                    url: "{{ Route('admin.product.sku-combination') }}",
                    data: $('#store_form').serialize(),
                    success: function(data) {
                        $('#sku_combination').html(data);
                        if (data.length > 1) {
                            $('#show-hide-div').hide();
                        } else {
                            $('#show-hide-div').show();
                        }
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

                        @foreach ($uoms as $item)
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
