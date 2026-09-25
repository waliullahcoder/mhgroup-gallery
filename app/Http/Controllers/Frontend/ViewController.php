<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Slider;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\ProductCategory;
use App\Models\SubCategory;
use App\Models\HomeSection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FrontEndService;
class ViewController extends Controller
{
    protected $frontEndService;
    public function __construct(FrontEndService $frontEndService)
    {
        $this->frontEndService = $frontEndService;
    }

    public function index()
    {
        $menus = $this->frontEndService->getMenu();
        $slides = Slider::where('status', true)->get();
        //Homepage Category all
        $get_sub_categories_all = $this->frontEndService->getSubCategoryHomePageOnly();
        // $get_sub_category_product_all = $this->frontEndService->getSubCategoryHomePageProductOnly();
        //All Header Product
        $get_sub_category_all_header_product_only = $this->frontEndService->getSubCategoryAllHeaderProductOnly();
        // //ট্রেন্ডিং বইসমূহ and নতুন প্রকাশিত বই
        $get_sub_category_trends_new_book_product_only = $this->frontEndService->getSubCategoryTrendsNewBookProductOnly();
        //Banner add category
        $get_sub_category_banner_only = $this->frontEndService->getSubCategoryBannerOnly();
        //সিয়ান যুগপূর্তি অফার and রবিউল আউয়াল সীরাত গ্রন্থমালা
        $get_sub_category_sian_jugpuerti_nrobiul_aual_product_only = $this->frontEndService->getSubCategorySianJugpuertiNrobiulAualProductOnly();
        //জনপ্রিয় লেখক
        $get_sub_category_writer_only = $this->frontEndService->getSubCategoryWriterOnly();
        //নিয়োগ সহায়িকা
        $get_sub_category_niog_sohaika_product_only = $this->frontEndService->getSubCategoryNiogSohaikaOnly();
        //বেস্ট সেলার বই
        $get_sub_category_best_seller_boi_product_only = $this->frontEndService->getSubCategoryBestSellerBoiOnly();

        //অন্যান্য পণ্য
        $get_sub_category_others_only = $this->frontEndService->getSubCategoryOthersOnly();
        //ব্র্যান্ডসমূহ
        $get_sub_category_brand_only = $this->frontEndService->getSubCategoryBrandOnly();
 
       $homecategories = Category::whereNotNull('parent_id')->whereIn('position',['header'])->orderBy('serial','asc')->get();
        $homeSections = HomeSection::orderBy('serial', 'asc')->get();
        return view('frontend.home', compact(
            'slides', 
            'homeSections',
            'menus',
            'homecategories',
            'get_sub_categories_all',
            'get_sub_category_trends_new_book_product_only',
            'get_sub_category_banner_only',
            'get_sub_category_sian_jugpuerti_nrobiul_aual_product_only',
            'get_sub_category_writer_only',
            'get_sub_category_all_header_product_only',
            'get_sub_category_niog_sohaika_product_only',
            'get_sub_category_others_only',
            'get_sub_category_brand_only',
            'get_sub_category_best_seller_boi_product_only'
        ));
    }

    public function loadHomeSection($type)
    {
        if ($type == 'niog') {
            $data = $this->frontEndService->getSubCategoryNiogSohaikaOnly();
            return view('frontend.home_sections.products', compact('data'));
        }

        if ($type == 'trending') {
            $data = $this->frontEndService->getSubCategoryTrendsNewBookProductOnly();
            return view('frontend.home_sections.products', compact('data'));
        }

        if ($type == 'sian') {
            $data = $this->frontEndService->getSubCategorySianJugpuertiNrobiulAualProductOnly();
            return view('frontend.home_sections.products', compact('data'));
        }

        if ($type == 'writers') {
            $data = $this->frontEndService->getSubCategoryWriterOnly();
            return view('frontend.home_sections.writers', compact('data'));
        }

        return '';
    }

    //Global search home
    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $products = Product::with(['authors', 'category'])
                            ->where('status', 1)
                            ->where(function ($q) use ($query) {

                                // Product Name
                                $q->where('name', 'LIKE', "%{$query}%")

                                // Product Slug
                                ->orWhere('slug', 'LIKE', "%{$query}%")
                                
                                // Product Code
                                ->orWhere('code', 'LIKE', "%{$query}%")

                                // Category Name
                                ->orWhereHas('category', function ($cat) use ($query) {
                                    $cat->where('name', 'LIKE', "%{$query}%");
                                })

                                // Author Name
                                ->orWhereHas('authors', function ($author) use ($query) {
                                    $author->where('name', 'LIKE', "%{$query}%");
                                });
                            })
                            ->limit(10)
                            ->get()
                            ->map(function($product){
                                return [
                                    'id' => $product->id,
                                    'name' => $product->name,
                                    'slug' => $product->slug,
                                    'thumbnail' => asset($product->thumbnail), // ✅ full URL
                                    'sale_price'=> $product->sale_price,
                                    'regular_price'=> $product->regular_price,
                                    'authors'   => $product->authors->pluck('name')->implode(', '), // all authors as string
                                ];
                            });
        return response()->json($products);
    }

    public function categoryPage($cat_id, $slug, $menu)
    {
        $menus = $this->frontEndService->getMenu();
        $authors = $this->frontEndService->getAuthor();
        $publications = $this->frontEndService->getPublication();
        $subcategories = $this->frontEndService->getProductData($cat_id);
        $bookcat_count = Category::where('type', 'book')->where('parent_id', $cat_id)->count();
        $relatedProducts = Product::latest()
                    ->take(8)
                    ->get();
        return view('frontend.categories.index', compact('menus','subcategories','authors','publications','bookcat_count','relatedProducts'));
    }
    public function singleCategoryPage($sub_cat_id)
    {
        $single_sub_category = $this->frontEndService->singleCategoryPage($sub_cat_id);
         $menus = $this->frontEndService->getMenu();
        $subcategories = $this->frontEndService->getProductData($sub_cat_id);
        $authors = $this->frontEndService->getAuthor();
        $publications = $this->frontEndService->getPublication();
        $bookcat_count = Category::where('type', 'book')->where('id', $sub_cat_id)->count();
        $relatedProducts = Product::latest()
                    ->take(8)
                    ->get();
        return view('frontend.categories.single_sub_category_page', compact('menus','subcategories','single_sub_category','authors','publications','bookcat_count','relatedProducts'));
    }

    //Category Page Left Filter
   public function filterProducts(Request $request)
    {
        $publicationIds = $request->publications ?? [];
        $authorIds      = $request->authors ?? [];
        $priceSort      = $request->price_sort ?? null; // low_high / high_low
        $priceRanges    = $request->price_ranges ?? [];

        $subcategories = Category::whereIn('type', ['other','book'])
            ->whereHas('products', function ($query) use ($publicationIds, $authorIds,$priceSort,$priceRanges) {

                // Publication filter
                if (!empty($publicationIds)) {
                    $query->whereIn('publication_id', $publicationIds);
                }

                // Author filter via many-to-many
                if (!empty($authorIds)) {
                    $query->whereHas('authors', function($q) use ($authorIds) {
                        $q->whereIn('author_id', $authorIds);
                    });
                }
                // Price sorting // low_high / high_low
                if(!empty($priceSort)) {
                if ($priceSort === 'low_high') {
                    $query->orderBy('sale_price', 'asc');
                } elseif ($priceSort === 'high_low') {
                    $query->orderBy('sale_price', 'desc');
                }
                }

                // Price Range filter
                if (!empty($priceRanges)) {
                    $query->where(function($q) use ($priceRanges){
                        foreach($priceRanges as $range){
                            if($range === '0-200'){
                                $q->orWhereBetween('sale_price', [0,200]);
                            } elseif($range === '201-500'){
                                $q->orWhereBetween('sale_price', [201,500]);
                            } elseif($range === '500+'){
                                $q->orWhere('sale_price', '>', 500);
                            }
                        }
                    });
                }


            })
            ->with(['products' => function ($query) use ($publicationIds, $authorIds,$priceSort,$priceRanges) {

                if (!empty($publicationIds)) {
                    $query->whereIn('publication_id', $publicationIds);
                }

                if (!empty($authorIds)) {
                    $query->whereHas('authors', function($q) use ($authorIds) {
                        $q->whereIn('author_id', $authorIds);
                    });
                }
                // Price sorting // low_high / high_low
                if(!empty($priceSort)) {
                    if ($priceSort === 'low_high') {
                    $query->orderBy('sale_price', 'asc');
                    } elseif ($priceSort === 'high_low') {
                        $query->orderBy('sale_price', 'desc');
                    }
                }

                 // Price Range filter
                if (!empty($priceRanges)) {
                $query->where(function($q) use ($priceRanges){
                    foreach($priceRanges as $range){
                        if($range === '0-200'){
                            $q->orWhereBetween('sale_price', [0,200]);
                        } elseif($range === '201-500'){
                            $q->orWhereBetween('sale_price', [201,500]);
                        } elseif($range === '500+'){
                            $q->orWhere('sale_price', '>', 500);
                        }
                    }
                });
                }


            }])
            ->get();

        return view('frontend.categories.partials.product_list', compact('subcategories'))->render();
    }

    //Sub Category Page Left Filter
    public function subFilterProducts(Request $request)
    {
       
        $publicationIds = $request->publications ?? [];
        $authorIds      = $request->authors ?? [];
        $priceSort      = $request->price_sort ?? null; // low_high / high_low
        $priceRanges    = $request->price_ranges ?? [];

        $single_sub_category = Category::whereIn('type', ['other','book'])
            ->whereHas('products', function ($query) use ($publicationIds, $authorIds,$priceSort,$priceRanges) {

                // Publication filter
                if (!empty($publicationIds)) {
                    $query->whereIn('publication_id', $publicationIds);
                }

                // Author filter via many-to-many
                if (!empty($authorIds)) {
                    $query->whereHas('authors', function($q) use ($authorIds) {
                        $q->whereIn('author_id', $authorIds);
                    });
                }
                // Price sorting // low_high / high_low
                if(!empty($priceSort)) {
                if ($priceSort === 'low_high') {
                    $query->orderBy('sale_price', 'asc');
                } elseif ($priceSort === 'high_low') {
                    $query->orderBy('sale_price', 'desc');
                }
                }

                // Price Range filter
                if (!empty($priceRanges)) {
                    $query->where(function($q) use ($priceRanges){
                        foreach($priceRanges as $range){
                            if($range === '0-200'){
                                $q->orWhereBetween('sale_price', [0,200]);
                            } elseif($range === '201-500'){
                                $q->orWhereBetween('sale_price', [201,500]);
                            } elseif($range === '500+'){
                                $q->orWhere('sale_price', '>', 500);
                            }
                        }
                    });
                }


            })
            ->with(['products' => function ($query) use ($publicationIds, $authorIds,$priceSort,$priceRanges) {

                if (!empty($publicationIds)) {
                    $query->whereIn('publication_id', $publicationIds);
                }

                if (!empty($authorIds)) {
                    $query->whereHas('authors', function($q) use ($authorIds) {
                        $q->whereIn('author_id', $authorIds);
                    });
                }
                // Price sorting // low_high / high_low
                if(!empty($priceSort)) {
                    if ($priceSort === 'low_high') {
                    $query->orderBy('sale_price', 'asc');
                    } elseif ($priceSort === 'high_low') {
                        $query->orderBy('sale_price', 'desc');
                    }
                }

                 // Price Range filter
                if (!empty($priceRanges)) {
                $query->where(function($q) use ($priceRanges){
                    foreach($priceRanges as $range){
                        if($range === '0-200'){
                            $q->orWhereBetween('sale_price', [0,200]);
                        } elseif($range === '201-500'){
                            $q->orWhereBetween('sale_price', [201,500]);
                        } elseif($range === '500+'){
                            $q->orWhere('sale_price', '>', 500);
                        }
                    }
                });
                }


            }])
            ->first();
    return view('frontend.categories.partials.sub_product_list', 
    compact('single_sub_category'))->render();
    }

    
    public function productDetails($id)
    {
        $menus = $this->frontEndService->getMenu();
        $product = $this->frontEndService->productDetails($id);
        // $relatedProducts = $this->frontEndService->productAll();
        $cat_id = ProductCategory::where('product_id', $id)->first()->category_id;
        
        $pivotCategoryIds = \DB::table('category_subcategory')
            ->where('parent_id', $cat_id)
            ->orWhere('subcategory_id', $cat_id)
            ->pluck('subcategory_id');
        $pivotCategories = Category::whereIn('id', $pivotCategoryIds);
        $category = $pivotCategories->with([
                'products' => function($query) {
                    $query->where('status', 1)->inRandomOrder();
                },
                'products.variants',
                'subcategories'
            ])
            ->orderBy('serial', 'asc')
            ->first();
        $relatedProducts=$category? $category->products->take(7) : null;
   
        
        $review_count=Review::where('product_id', $id)->where('user_id',Auth::id())->count();
        return view('frontend.products.productDetails', compact('product','menus','relatedProducts','review_count'));
    }

    public function signinPage()
    {
          $menus = $this->frontEndService->getMenu();
        return view('frontend.auth.signin',compact('menus'));
    }
    public function signupPage()
    {
        $menus = $this->frontEndService->getMenu();
        return view('frontend.auth.signup',compact('menus'));
    }
    public function forgotPasswordPage(Request $request)
    {
         $menus = $this->frontEndService->getMenu();
         $user = null;
        return view('frontend.auth.forgot_password',compact('menus','user')); 
    }

    public function infoPage($id)
    {
        $menus = $this->frontEndService->getMenu();
        $info = Category::find($id);
        return view('frontend.information.index',compact('menus','info'));
    }
}
