<?php

namespace App\Http\Controllers\Admin;

use App\Models\Uom;
use App\HelperClass;
use App\Models\Brand;
use App\Models\Author;
use App\Models\Publication;
use App\Models\ProductAuthor;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\ProductTag;
use App\Models\ProductVariant;
use App\Models\ProductEdition;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\ProductVariantService;
use Yajra\DataTables\Facades\DataTables;
class ProductController extends Controller
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
        $this->path = 'product';
        $this->title = 'Product Items';
        $this->create_title = 'Add Product';
        $this->edit_title = 'Update Product';
        $this->model = Product::class;
        $this->productService = $productService;
        $this->productVariantService = $productVariantService;
    }

    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     return HelperClass::resourceDataView($this->model::with(['category', 'uom'])->where('product_type', 'book')->orderBy('id', 'desc'), 'thumbnail', null, $this->path, $this->title);
    // }

     public function index()
        {
            return HelperClass::resourceDataView(
                $this->model::with(['categories', 'uom','edition'])
                    ->select('id','code','name','uom_id','thumbnail','status')
                    ->orderBy('id', 'desc'),
                'thumbnail',
                null,
                $this->path,
                $this->title,
                null // এখানে relation_data null রাখো
            );
        }

    
    public function skuCombination(Request $request)
    {
        if ($request->ajax()) {
            $options = array();
            $purchase_price = $request->purchase_price;
            $regular_price = $request->regular_price;

            if ($request->has('choice_no')) {
                foreach ($request->choice_no as $no) {
                    $name = 'choice_options_' . $no;
                    $data = array();
                    if (!is_null($request[$name])) {
                        foreach ($request[$name] as $item) {
                            array_push($data, $item);
                        }
                    }
                    if (count($data) == 0) {
                        continue;
                    }
                    array_push($options, $data);
                }
            }
            $combinations = HelperClass::makeCombinations($options);
            return view('admin.product.partial.sku-combinations', compact('combinations', 'purchase_price', 'regular_price'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax() && $request->has('get_choices')) {
            $allValues = AttributeValue::with(['attribute'])->where('attribute_id', $request->attribute_id)->get();
            $html = '';
            foreach ($allValues as $row) {
                $html .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
            return json_encode($html);
        }

        $title = $this->create_title;
        $brands = Brand::where('status', true)->orderBy('name', 'asc')->get();
        $uoms = Uom::where('status', true)->orderBy('name', 'asc')->get();
       $subcategories = Category::whereNotNull('parent_id')->whereIn('position', ['header','homepage'])->where('status', true)->orderBy('name', 'asc')->get();
        $vendors = Vendor::where('status', true)->orderBy('name', 'asc')->get();
        $attributes = Attribute::where('status', true)->orderBy('name', 'asc')->get();
        $authors = Author::where('status', true)->orderBy('name', 'asc')->get();
        $publications = Publication::where('status', true)->orderBy('name', 'asc')->get();
        return view("admin.{$this->path}.create", compact('title', 'brands', 'uoms', 'subcategories', 'vendors', 'attributes', 'authors', 'publications'));
    }

     /**
     * Generate Code No.
     */
    public function codeNo()
    {
        // Current date in YYYYMMDD format
         $date = date('Ymd');
        // Last product
        $last = $this->model::withTrashed()->orderBy('id', 'desc')->first();
        if ($last && $last->id) {
            // CODE + date + last id
            return 'COD' . $date . $last->id +1;
        } else {
            // No product yet
            return 'COD' . $date . '1';
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required',
            'uom_id'      => 'required',
               // 'category_id' => 'required',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'barcode'     => 'nullable|unique:products,barcode',
        ]);

        if(!empty($request->name)) {
           $exists=Product::where('name', $request->name)->exists();
           if($exists) {
            return back()->withErrors('Product already exists');
           }
        }

        try {
            DB::transaction(function () use ($request) {
            
            // $product = $this->productService->store($request->except(['_token', 'choice']));
            // ✅ generate code
            $data = $request->except(['_token', 'choice']);

            // uom_id[] থেকে Product-এর জন্য প্রথম UOM নিন
            if (is_array($data['uom_id'] ?? null)) {
                $data['uom_id'] = $data['uom_id'][0] ?? null;
            }

            $data['code'] = $this->codeNo();

            // ✅ PRODUCT STORE
            $product = $this->productService->store($data);

                // ✅ VARIANT
                if ($request->filled('uom_id') && is_array($request->uom_id)) {

                    foreach ($request->uom_id as $key => $uomId) {

                        if (empty($uomId)) {
                            continue;
                        }

                        $regularPrice = $request->price[$key] ?? 0;

                        $discount = $request->discount ?? 0;
                        $discountType = $request->discount_type ?? 'amount';

                        if ($discountType === 'percent') {
                            $salePrice = $regularPrice - ($regularPrice * $discount / 100);
                        } else {
                            $salePrice = $regularPrice - $discount;
                        }

                        $salePrice = max(0, $salePrice);

                        ProductVariant::create([
                            'product_id'     => $product->id,
                            'uom_id'         => $uomId,
                            'variant'        => $request->size[$key] ?? null,
                            'sku'            => $request->sku,
                            'purchase_price' => $request->purchase_price,
                            'regular_price'  => $regularPrice,
                            'discount_type'  => $discountType,
                            'discount'       => $discount,
                            'sale_price'     => $salePrice,
                            'status'         => true,
                        ]);
                    }
                }

                // ✅ IMAGES (NULL SAFE)
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image' => HelperClass::saveImage($image, 800, 'media/product'),
                        ]);
                    }
                }

                // ✅ VENDORS (NULL SAFE)
                if (!empty($request->vendor_id)) {
                    $product->vendors()->attach($request->vendor_id);
                }

                 // categories insert
                if (!empty($request->category_ids)) {
                    $product->categories()->attach($request->category_ids);
                }

                //Edition
                if (!empty($request->edition_name)) {
                    ProductEdition::create([
                        'product_id' => $product->id,
                        'name' => $request->edition_name,
                        'status' => true,
                    ]);
                }

                // ✅ TAGS (NULL SAFE)
                if (!empty($request->tags) && isset($request->tags[0])) {
                    $tags = json_decode($request->tags[0]);
                    if (is_array($tags) || is_object($tags)) {
                        foreach ($tags as $tag) {
                            ProductTag::create([
                                'product_id' => $product->id,
                                'name' => $tag->value
                            ]);
                        }
                    }
                }

                // ✅ AUTHOR (NULL SAFE)
                if (!empty($request->author_id)) {
                    ProductAuthor::create([
                        'product_id' => $product->id,
                        'author_id'  => $request->author_id
                    ]);
                }
            });

            return redirect()
                ->route("admin.{$this->path}.index")
                ->withSuccessMessage('✅ Created Successfully!');

        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'error' => '❌ ' . $e->getMessage()
                ]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function skuCombinationEdit(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $options = array();
        $purchase_price = $request->purchase_price;
        $regular_price = $request->regular_price;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $no) {
                $name = 'choice_options_' . $no;
                $data = array();
                if (!is_null($request[$name])) {
                    foreach ($request[$name] as $item) {
                        array_push($data, $item);
                    }
                }
                if (count($data) == 0) {
                    continue;
                }
                array_push($options, $data);
            }
        }

        $combinations = HelperClass::makeCombinations($options);
        return view('admin.product.partial.sku-combinations-edit', compact('combinations', 'purchase_price', 'regular_price', 'product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        if ($request->ajax() && $request->has('get_choices')) {
            $allValues = AttributeValue::with(['attribute'])->where('attribute_id', $request->attribute_id)->get();
            $html = '';
            foreach ($allValues as $row) {
                $html .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
            return json_encode($html);
        }

        $additionalData = [
            'brands' => Brand::where('status', true)->orderBy('name', 'asc')->get(),
            'uoms' => Uom::where('status', true)->orderBy('name', 'asc')->get(),
            'categories' => Category::whereNotNull('parent_id')->whereIn('position', ['header','homepage'])->where('status', true)->orderBy('name', 'asc')->get(),
            'vendors' => Vendor::where('status', true)->orderBy('name', 'asc')->get(),
            'publications' => Publication::where('status', true)->orderBy('name', 'asc')->get(),
            'attributes' => Attribute::where('status', true)->orderBy('name', 'asc')->get()
        ];
        return HelperClass::resourceDataEdit($this->model, $id, $this->path, $this->edit_title, $additionalData);
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, string $id)
    {
       
        $request->validate([
            'name'        => 'required',
            'uom_id'      => 'required',
            // 'category_id' => 'required',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'barcode'     => 'nullable|unique:products,barcode,' . $id,
        ]);

        try {
            DB::transaction(function () use ($request, $id) {

                $product = $this->model::findOrFail($id);
                $data = $request->except(['_token','choice']);
                if(empty($request->code)){
                    $date = date('Ymd');
                    $data['code'] = 'COD' . $date . $id;
                }
                // ✅ PRODUCT UPDATE
                $product = $this->productService->update($data, $product);
                // $product = $this->productService->update($request->except(['_token', 'choice']), $product);

                // ✅ VARIANT (NULL SAFE)
                if ($request->filled('choice_no')) {
                    $this->productVariantService->store(
                        $request->only(['choice_no', 'purchase_price', 'sale_price', 'sku']),
                        $product
                    );
                }

                // ======================
                // ✅ IMAGES (NULL SAFE)
                // ======================
                if ($request->hasFile('images')) {

                    // delete old images
                    if ($product->images && $product->images->count() > 0) {
                        foreach ($product->images as $img) {
                            if (!empty($img->image) && file_exists($img->image)) {
                                @unlink($img->image);
                            }
                        }
                    }

                    ProductImage::where('product_id', $id)->delete();

                    foreach ($request->file('images') as $image) {
                        ProductImage::create([
                            'product_id' => $id,
                            'image' => HelperClass::saveImage($image, 800, 'media/product'),
                        ]);
                    }
                }

                // ======================
                // ✅ VENDORS (NULL SAFE)
                // ======================
                if (!empty($request->vendor_id)) {
                    $product->vendors()->sync($request->vendor_id);
                } else {
                    $product->vendors()->detach();
                }

                 // ======================
                // ✅ CATEGORY SYNC (IMPORTANT)
                // ======================
                if (!empty($request->category_ids)) {
                    $product->categories()->sync($request->category_ids);
                } else {
                    $product->categories()->detach();
                }

                 //Edition
                if (!empty($request->edition_name)) {
                    $existedition = ProductEdition::where('product_id', $product->id)->first();
                    if ($existedition) {
                        $existedition->update(['name' => $request->edition_name]);
                    }else{
                    ProductEdition::create([
                        'product_id' => $product->id,
                        'name' => $request->edition_name,
                        'status' => true,
                        ]);
                    }
                    
                }else{
                    return back()->withErrors('Edition name is required');
                }

                // ======================
                // ✅ TAGS (NULL SAFE)
                // ======================
                ProductTag::where('product_id', $id)->delete();

                if (!empty($request->tags) && isset($request->tags[0])) {
                    $tags = json_decode($request->tags[0]);
                    if (is_array($tags) || is_object($tags)) {
                        foreach ($tags as $tag) {
                            ProductTag::create([
                                'product_id' => $product->id,
                                'name' => $tag->value
                            ]);
                        }
                    }
                }
            });

            return redirect()
                ->route("admin.{$this->path}.index")
                ->withSuccessMessage('✅ Updated Successfully!');

        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'error' => '❌ ' . $e->getMessage()
                ]);
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
