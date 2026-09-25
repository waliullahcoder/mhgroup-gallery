<div class="row g-3">
    @if ($type == 'Category Product')
        <div class="col-sm-6">
            <label for="category_id" class="form-label"><b>Category <span class="text-danger">*</span></b></label>
            <select name="category_id" id="category_id" class="form-select select" data-placeholder="Select Category"
                required>
                <option value=""></option>
                @php
                    $categories = \App\Models\Category::root()
                        ->with(['children'])
                        ->where('status', true)
                        ->orderBy('name', 'asc')
                        ->get();
                @endphp
                <option value=""></option>
                @foreach ($categories as $category)
                    @include('admin.home-section._category', [
                        'category' => $category,
                        'prefix' => '',
                    ])
                @endforeach
            </select>
        </div>
    @elseif (in_array($type, ['Trending Product', 'New Product']))
        <div class="col-sm-6">
            <label for="product_type" class="form-label"><b>Product Type <span class="text-danger">*</span></b></label>
            <select name="product_type[]" id="product_type" class="form-select select"
                data-placeholder="Select Product Type" multiple required>
                <option value="book">Books</option>
                <option value="other">Other Products</option>
            </select>
        </div>
    @elseif ($type == 'Featured Category')
        <div class="col-sm-6">
            <label for="category_ids" class="form-label"><b>Featured Categories <span
                        class="text-danger">*</span></b></label>
            <select name="category_ids[]" id="category_ids" class="form-select select"
                data-placeholder="Select Featured Categories" multiple required>
                <option value=""></option>
                @php
                    $categories = \App\Models\Category::root()
                        ->with(['children'])
                        ->where('status', true)
                        ->orderBy('name', 'asc')
                        ->get();
                @endphp
                <option value=""></option>
                @foreach ($categories as $category)
                    @include('admin.home-section._category', [
                        'category' => $category,
                        'prefix' => '',
                    ])
                @endforeach
            </select>
        </div>
    @elseif ($type == 'Category Carousel')
        <div class="col-sm-6">
            <label for="category_ids" class="form-label"><b>Carousel Categories <span
                        class="text-danger">*</span></b></label>
            <select name="category_ids[]" id="category_ids" class="form-select select"
                data-placeholder="Select Carousel Categories" multiple required>
                <option value=""></option>
                @php
                    $categories = \App\Models\Category::root()
                        ->with(['children'])
                        ->where('status', true)
                        ->orderBy('name', 'asc')
                        ->get();
                @endphp
                <option value=""></option>
                @foreach ($categories as $category)
                    @include('admin.home-section._category', [
                        'category' => $category,
                        'prefix' => '',
                    ])
                @endforeach
            </select>
        </div>
    @elseif ($type == 'Popular Writter')
        <div class="col-sm-6">
            <label for="author_ids" class="form-label"><b>Writters <span class="text-danger">*</span></b></label>
            <select name="author_ids[]" id="author_ids" class="form-select select" data-placeholder="Select Writters"
                multiple required>
                <option value=""></option>
                @php
                    $authors = \App\Models\Author::where('status', true)->orderBy('name', 'asc')->get();
                @endphp
                @foreach ($authors as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
    @elseif ($type == 'Banner')
        <div class="col-12">
            <div class="row g-3">
                <div class="col-md-9">
                    <div class="row g-2">
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
                    </div>
                    <div id="fileUploadsContainer">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary-faded btn-block add-extra-attachement">
                        <i class="far fa-plus"></i>Add More
                    </button>
                </div>
            </div>
        </div>
    @elseif ($type == 'Brand')
        <div class="col-sm-6">
            <label for="brand_ids" class="form-label"><b>Brands <span class="text-danger">*</span></b></label>
            <select name="brand_ids[]" id="brand_ids" class="form-select select" data-placeholder="Select Brands"
                multiple required>
                <option value=""></option>
                @php
                    $brands = \App\Models\Brand::where('status', true)->orderBy('name', 'asc')->get();
                @endphp
                @foreach ($brands as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
</div>
