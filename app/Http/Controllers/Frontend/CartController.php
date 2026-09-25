<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Slider;
use App\Models\HomeSection;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Services\FrontEndService;
class CartController extends Controller
{
    protected $frontEndService;
    public function __construct(FrontEndService $frontEndService)
    {
        $this->frontEndService = $frontEndService;
    }

    public function add(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $cart = session()->get('cart', []);
        if (isset($cart[$product->id])) {
            $cart[$product->id]['qty']++;
        } else {
            $cart[$product->id] = [
                'id'    => $product->id,
                'code'  => $product->code,
                'product_type'  => $product->product_type,
                'name'  => $product->name,
                'price' => $product->sale_price,
                'qty'   => 1,
                'variant_id' => $request->variant_id,
                'image' => $product->thumbnail,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'count' => count($cart)
        ]);
    }

    public function index()
    {
         $menus = $this->frontEndService->getMenu();
        $cart = session('cart', []);
        return view('frontend.cart.index', compact('cart','menus'));
    }

    public function update(Request $request)
    {
        $cart = session('cart', []);
        $id   = $request->id;

        if(isset($cart[$id])) {
            $cart[$id]['qty'] = max(1, $request->qty);
            session()->put('cart', $cart);
        }

        return response()->json(['success' => true]);
    }

    public function clear()
    {
        session()->forget('cart');
        return back();
    }


    public function remove($id)
    {
        $cart = session('cart');

        unset($cart[$id]);
        session()->put('cart', $cart);

        return back();
    }
}