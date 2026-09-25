@php
    $route = \Request::route()->getName();
    $storeRoute = str_replace('index', 'store', $route);
@endphp

<form action="{{ Route($storeRoute) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <!-- Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addModalLabel">Add New Category</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="parent_id" class="form-label">Parent Category:</label>
                            <select name="parent_id" id="parent_id" class="form-select select"
                                data-placeholder="Select Parent Category">
                                <option value="">None</option>
                                @php
                    
                                        $categories = \App\Models\Category::with(['children'])
                                        ->whereNull('parent_id')
                                        ->whereIn('position', ['mega_menu_parent', 'header', 'homepage'])
                                        ->where('status', 1)
                                        ->orderBy('name', 'asc')
                                        ->get();
                                @endphp
                                @foreach ($categories as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                                    <label for="type" class="form-label"><b>Type</b></label>
                                    <select name="type" id="type" class="form-select" required>
                                        ornaments
                                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                                <option value="fragrance" {{ old('type') == 'fragrance' ? 'selected' : '' }}>Fragrance</option>
                                <option value="mirror" {{ old('type') == 'mirror' ? 'selected' : '' }}>Mirror</option>
                                <option value="cloths" {{ old('type') == 'cloths' ? 'selected' : '' }}>Cloths</option>
                                <option value="frozen" {{ old('type') == 'frozen' ? 'selected' : '' }}>Frozen</option>
                                 <option value="ornaments" {{ old('type') == 'ornaments' ? 'selected' : '' }}>Ornaments</option>
                                <option value="book" {{ old('type') == 'book' ? 'selected' : '' }}>Book
                                </option>
                                    
                                </select>
                        </div>
                        <div class="col-12">
                                    <label for="position" class="form-label"><b>Position</b></label>
                                    <select name="position" id="position" class="form-select" required>
                                      <option value="header_top" {{ old('position') == 'header_top' ? 'selected' : '' }}>
                                        Header Top
                                    </option>

                                    <option value="header" {{ old('position') == 'header' ? 'selected' : '' }}>
                                        Header Middle 
                                    </option>

                                     <option value="mega_menu_parent" {{ old('position') == 'mega_menu_parent' ? 'selected' : '' }}>
                                        Mega Menu Parent
                                    </option>
                                    <option value="mega_menu_child" {{ old('position') == 'mega_menu_child' ? 'selected' : '' }}>
                                        Mega Menu Child
                                    </option>
                                    <option value="homepage" {{ old('position') == 'homepage' ? 'selected' : '' }}>
                                        Home Page
                                    </option>
                                    <option value="homepage_banner_category" {{ old('position') == 'homepage_banner_category' ? 'selected' : '' }}>
                                        Home Page->Banner Category
                                    </option>
                                    <option value="homepage_writter_category" {{ old('position') == 'homepage_writter_category' ? 'selected' : '' }}>
                                        Home Page->Writter Category
                                    </option>
                                    <option value="homepage_others_category" {{ old('position') == 'homepage_others_category' ? 'selected' : '' }}>
                                        Home Page->Others Category
                                    </option>
                                    <option value="homepage_brands_category" {{ old('position') == 'homepage_brands_category' ? 'selected' : '' }}>
                                        Home Page->Brands Category
                                    </option>

                                    <option value="footer" {{ old('position') == 'footer' ? 'selected' : '' }}>
                                        Footer Column1
                                    </option>

                                    <option value="footer_col2" {{ old('position') == 'footer_col2' ? 'selected' : '' }}>
                                        Footer Column2
                                    </option>

                                    </select>
                                </div>
                            <div class="col-12">
                            <label for="url" class="form-label">URL: <span class="text-danger">*</span></label>
                            <input type="text" name="url" id="url" class="form-control" value="#" required>
                        </div>
                        <div class="col-12">
                            <label for="name" class="form-label">Name: <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        <div class="col-12">
                            <label for="image" class="form-label">Image:</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Description:</label>
                            <textarea class="form-control description" id="description" name="description" placeholder="Description"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-1">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</form>
