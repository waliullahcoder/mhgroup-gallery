<?php

namespace App\Services;

use App\HelperClass;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Publication;
use App\Models\Product;
use App\Models\Author;
use App\Services\Utility\ProductUtility;
use Illuminate\Support\Facades\Auth;
use DB;

class FrontEndService
{
    public function getMenu()
    {

/*
|--------------------------------------------------------------------------
| MAIN MENUS (parent_id = NULL)
|--------------------------------------------------------------------------
*/
$menus = Category::whereNull('parent_id')
    ->where('status', 1)
    ->select(
        'id',
        'id as category_id',
        'name',
        'url as menu_url',
        'name as category_name',
        'slug as category_slug',
        'position'
    )
    ->orderBy('serial', 'asc')
    ->get()
    ->groupBy('position');

$data['top_menus']         = $menus['header_top']        ?? collect();
$data['middle_menus']      = $menus['header']            ?? collect();
$data['mega_menus']        = $menus['mega_menu_parent']  ?? collect();
$data['footer_col1_menus'] = $menus['footer']       ?? collect();
$data['footer_col2_menus'] = $menus['footer_col2']       ?? collect();

$data['sub_menus'] = Category::whereNotNull('parent_id')
    ->where('status', 1)
    ->where('position', 'mega_menu_child')
    ->select(
        'id',
        'name',
        'parent_id',
        'slug as category_slug'
    )
    ->get()
    ->groupBy('parent_id');

return $data;



        // $menus = Menu::where('menus.status', true)
        // ->join('categories', 'menus.category_id', '=', 'categories.id')
        // ->select(
        //     'menus.*',
        //     'categories.name as category_name',
        //     'categories.slug as category_slug'
        // )
        // ->get()
        // ->groupBy('position');

        // $data['top_menus']         = $menus['header_top']   ?? collect();
        // $data['middle_menus']      = $menus['header']       ?? collect();
        // $data['mega_menus']        = $menus['mega_menu']    ?? collect();
        // $data['footer_col1_menus'] = $menus['footer_col1']  ?? collect();
        // $data['footer_col2_menus'] = $menus['footer_col2']  ?? collect();


        // $data['sub_menus'] = DB::table('menu_items')
        //     ->get()
        //     ->groupBy('menu_id');
        // return $data;
    }

     public function getSubCategoryData($category_id){
             return DB::table('categories')
            ->where('parent_id', $category_id)
            ->orderBy('serial', 'asc')
            ->get();
     }

 public function getProductData($cat_id)
{
    // 1️⃣ Direct child categories
   // $directCategories = Category::where('parent_id', $cat_id);

    // 2️⃣ Pivot table থেকে category id নেওয়া
    $pivotCategoryIds = \DB::table('category_subcategory')
        ->where('parent_id', $cat_id)
        ->pluck('subcategory_id');

    // 3️⃣ Pivot categories query
    $pivotCategories = Category::whereIn('id', $pivotCategoryIds);

    // 4️⃣ দুইটা merge করা
    //  $categories = $directCategories
    //     ->union($pivotCategories)
    $categories = $pivotCategories->with([
            'products' => function($query) {
                $query->where('status', 1)->inRandomOrder();
            },
            'products.variants',
            'subcategories'
        ])
        ->orderBy('serial', 'asc')
        ->get();

    return $categories;
}

//বেস্ট সেলার বই
public function getSubCategoryBestSellerBoiOnly()
{
    return Product::where('status', 1)
        ->orderByDesc(
            DB::raw('(SELECT COALESCE(SUM(qty),0) 
                      FROM order_items 
                      WHERE order_items.product_id = products.id)')
        )
        ->with('variants')
        ->limit(10)
        ->get();
}

//নিয়োগ সহায়িকা
public function getSubCategoryNiogSohaikaOnly()
{
    return Category::whereNotNull('parent_id')
    ->whereIn('parent_id', [49])
    ->with(['products' => function($query) {
            $query->where('status', 1)->inRandomOrder();
        }, 'products.variants'])
    ->orderBy('id', 'asc')
    ->get();
}
//ট্রেন্ডিং বইসমূহ and নতুন প্রকাশিত বই
public function getSubCategoryTrendsNewBookProductOnly()
{
return Category::whereNotNull('parent_id')
     ->whereIn('parent_id', [48])
    ->with(['products' => function($query) {
            $query->where('status', 1)->inRandomOrder();
        }, 'products.variants'])
    ->orderBy('serial', 'asc')
    ->get();
}
public function getSubCategoryAllHeaderProductOnly()
{
// return Category::whereNotNull('parent_id')
//     ->whereIn('parent_id', [5])
//     ->with(['products' => function($query) {
//             $query->where('status', 1)->inRandomOrder();
//         }, 'products.variants'])
//     ->orderBy('serial', 'asc')
//     ->get();
return Category::where('parent_id', 5)
    ->with([
        'products' => function ($query) {
            $query->select([
                'products.id',
                'products.code',
                'products.name',
                'products.discount',
                'products.regular_price',
                'products.sale_price',
                'products.thumbnail',
            ])
            ->where('products.status', 1)
            ->inRandomOrder()
            ->limit(6);
        },

        'products.variants'
    ])
    ->orderBy('serial', 'asc')
    ->get();
}

//সিয়ান যুগপূর্তি অফার and রবিউল আউয়াল সীরাত গ্রন্থমালা
public function getSubCategorySianJugpuertiNrobiulAualProductOnly()
{
return Category::whereNotNull('parent_id')
    ->whereIn('parent_id', [4,1,94])
    ->with('products','products.variants')
    ->orderBy('serial', 'asc')
    ->get();
}
//জনপ্রিয় লেখক
public function getSubCategoryWriterOnly()
{
    return Category::whereNotNull('parent_id')
    ->whereIn('parent_id', [94])
    ->with('products','products.variants')
    ->orderBy('serial', 'asc')
    ->get();
}
//--------------Home Page----------------//

//Homepage Category all
public function getSubCategoryHomePageOnly()
{
$categories = Category::whereNotNull('parent_id')->where('position', 'homepage')->orderBy('id', 'desc')->get();
       return $categories;
}



//Banner add category
public function getSubCategoryBannerOnly()
{
    return Category::whereNotNull('parent_id')->where('position', 'homepage_banner_category')->get();
}



//আতর ও সুগন্ধি পণ্য
public function getSubCategoryAtarSugondhiProductOnly()
{
    return Category::whereNotNull('parent_id')
    ->whereIn('slug', ['atr-oo-sugndhi-pnz'])
    ->with('products','products.variants')
    ->orderBy('serial', 'asc')
    ->get();
}

//অন্যান্য পণ্য
public function getSubCategoryOthersOnly()
{
    return Category::whereNotNull('parent_id')->where('position', 'homepage_others_category')->get();
}

//ব্র্যান্ডসমূহ
public function getSubCategoryBrandOnly()
{
    return Category::whereNotNull('parent_id')->where('position', 'homepage_brands_category')->get();
}

//--------------Home Page\\\\\\\\\\----------------//

public function getSubCategoryDataAll()
{
   
$categories = Category::whereNotNull('parent_id')->get();
       return $categories;
}

public function getSubCategoryProductAll()
{
$get_sub_category_product_all = Category::whereNotNull('parent_id')->with('products','products.variants')->get();
       return $get_sub_category_product_all;
}

 public function singleCategoryPage($sub_cat_id)
{
   
    $single_sub_category = Category::with('products','products.variants')
        ->where('id', $sub_cat_id)
        ->first();
        return $single_sub_category;
    }

public function productDetails($id)
{
   
    $product = Product::with('variants')
        ->where('id', $id)
        ->first();
        return $product;
}   

public function getAuthor()
{
   $authors = Author::orderBy('id', 'desc')
    ->limit(10)
    ->get();
    return $authors;
}
public function getPublication()
{
    $publications = Publication::orderBy('id', 'desc')->limit(10)->get();
        return $publications;
}
public function productAll()
{
    $product = Product::get();
    if($product->count()>4){
    $product = Product::orderBy('id', 'desc')
        ->get()->random(8);
    }
        return $product;
}  

}