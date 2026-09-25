<?php

namespace App\Http\Controllers\Admin;

use App\Models\Uom;
use App\HelperClass;
use App\Models\Vendor;
use App\Models\Author;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductTag;
use App\Models\Publication;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\ProductVariantService;

class BookController extends Controller
{
    public $path;
    public $title;
    public $model;
    public $edit_title;
    public $create_title;
    protected $productService;
    protected $productVariantService;
    public function __construct(ProductService $productService, ProductVariantService $productVariantService)
    {
        $this->path = 'book';
        $this->title = 'Book Items';
        $this->create_title = 'Add Book';
        $this->edit_title = 'Update Book';
        $this->model = Product::class;
        $this->productService = $productService;
        $this->productVariantService = $productVariantService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return HelperClass::resourceDataView($this->model::with(['category', 'uom', 'authors', 'publication'])->where('type', 'book')->orderBy('id', 'desc'), 'thumbnail', null, $this->path, $this->title);
    }

    public function menuUi($categories, $category_id = null, $category_ids = [])
    {
        return $this->renderMenu($categories, $category_id, $category_ids);
    }

    protected function renderMenu($categories, $category_id, $category_ids)
    {
        $html = '';
        foreach ($categories as $element) {
            $html .= $this->renderItem($element, $category_id, $category_ids);
        }
        return $html;
    }

    protected function renderItem($element, $category_id, $category_ids)
    {
        $id = htmlspecialchars($element->id);
        $name = htmlspecialchars($element->name);
        $checked = in_array($element->id, $category_ids) ? 'checked' : '';
        $selected = $category_id == $element->id ? 'checked' : '';

        $html = '<li>
                <div class="custom-control custom-checkbox">
                    <input class="custom-control-input" id="' . $id . '" value="' . $id . '" type="checkbox" name="category_ids[]" ' . $checked . '>
                    <label for="' . $id . '" class="custom-control-label ps-1">' . $name . '</label>
                </div>
                <div class="form-check form-switch form-switch-sm">
                    <input class="form-check-input change-status c-pointer" value="' . $id . '" type="radio" name="category_id" ' . $selected . ' required>
                </div>';

        if (!empty($element->children)) {
            $html .= '<ul>' . $this->renderMenu($element->children, $category_id, $category_ids) . '</ul>';
        }

        $html .= '</li>';

        return $html;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $title = $this->create_title;
        $authors = Author::where('status', true)->orderBy('name', 'asc')->get();
        $publications = Publication::where('status', true)->orderBy('name', 'asc')->get();
        $uoms = Uom::where('status', true)->orderBy('name', 'asc')->get();
        $categories = Category::root()->with(['children'])->orderBy('name', 'asc')->get();
        $html = $this->menuUi($categories);
        $vendors = Vendor::where('status', true)->orderBy('name', 'asc')->get();
        return view("admin.{$this->path}.create", compact('title', 'authors', 'publications', 'uoms', 'categories', 'html', 'vendors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'uom_id' => 'required',
            'author_ids' => 'required',
            'category_id' => 'required',
            'publication_id' => 'required',
            'barcode' => 'nullable|unique:products,barcode',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $product = $this->productService->store($request->except(['_token', 'choice']));
                $this->productVariantService->store($request->only(['choice_no', 'purchase_price', 'sale_price', 'sku']), $product);

                $images = $request->file('images');
                if (isset($images)) {
                    foreach ($images as $image) {
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image' => HelperClass::saveImage($image, 800, 'media/product'),
                        ]);
                    }
                }

                $product->vendors()->attach($request->vendor_id);

                if ($request->tags) {
                    foreach (json_decode($request->tags[0]) as $tag) {
                        ProductTag::create([
                            'product_id' => $product->id,
                            'name' => $tag->value
                        ]);
                    }
                }

                $product->categories()->attach($request->category_ids);
                $product->authors()->attach($request->author_ids);
            });

            return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Created Successfully!');
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = $this->model::findOrFail($id);
        $categories = Category::root()->with(['children'])->orderBy('name', 'asc')->get();
        $additionalData = [
            'authors' => Author::where('status', true)->orderBy('name', 'asc')->get(),
            'publications' => Publication::where('status', true)->orderBy('name', 'asc')->get(),
            'uoms' => Uom::where('status', true)->orderBy('name', 'asc')->get(),
            'categories' => Category::where('status', true)->orderBy('name', 'asc')->get(),
            'vendors' => Vendor::where('status', true)->orderBy('name', 'asc')->get(),
            'html' => $this->menuUi($categories, $data->category_id, $data->categories->pluck('id')->toArray()),
        ];

        return HelperClass::resourceDataEdit($this->model, $id, $this->path, $this->edit_title, $additionalData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'uom_id' => 'required',
            'author_ids' => 'required',
            'category_id' => 'required',
            'publication_id' => 'required',
            'barcode' => 'nullable|unique:products,barcode,' . $id,
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $data = $this->model::findOrFail($id);
                info($request->file);
                $product = $this->productService->update($request->except(['_token', 'choice']), $data);
                $this->productVariantService->store($request->only(['choice_no', 'purchase_price', 'sale_price', 'sku']), $product);

                $images = $request->file('images');
                if (isset($images)) {
                    $old_images = $product->images->pluck('image')->toArray();
                    foreach ($old_images as $item) {
                        if (file_exists($item)) {
                            unlink($item);
                        }
                    }
                    ProductImage::where('product_id', $id)->delete();
                    foreach ($images as $image) {
                        ProductImage::create([
                            'product_id' => $id,
                            'image' => HelperClass::saveImage($image, 800, 'media/product'),
                        ]);
                    }
                }
                $product->vendors()->sync($request->vendor_id);

                ProductTag::where('product_id', $id)->delete();
                if ($request->tags) {
                    foreach (json_decode($request->tags[0]) as $tag) {
                        ProductTag::create([
                            'product_id' => $product->id,
                            'name' => $tag->value
                        ]);
                    }
                }

                $product->categories()->sync($request->category_ids);
                $product->authors()->sync($request->author_ids);
            });

            return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Updated Successfully!');
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return HelperClass::resourceDataDelete($this->model, $id, ['thumbnail', 'meta_image'], ['images', 'variants']);
    }
}
