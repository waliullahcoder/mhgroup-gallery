<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Slider;
use App\Models\HomeSection;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Coa;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\FrontEndService;
class CheckoutController extends Controller
{
    protected $frontEndService;
    public function __construct(FrontEndService $frontEndService)
    {
        $this->frontEndService = $frontEndService;
    }
  
    public function checkout()
    {
        $cart = session('cart', []);

        if (count($cart) == 0) {
            return redirect()->back()->with('error', 'Cart is empty');
        }

         $menus = $this->frontEndService->getMenu();
        return view('frontend.checkout.index', compact('menus','cart'));
    }

    public function buyNow(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['qty']++;
        } else {
            $cart[$product->id] = [
                'id'         => $product->id,
                'code'       => $product->code,
                'product_type'  => $product->product_type,
                'name'       => $product->name,
                'price'      => $product->sale_price,
                'qty'        => 1,
                'variant_id' => $request->variant_id,
                'image'      => $product->thumbnail,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('checkout');
    }


    public function placeOrder(Request $request)
    {
         
        if($request->phone){
            $user = User::where('phone', $request->phone)->first();
            if($user){
                auth()->login($user);
            }
           // return back()->withErrors('Please login to continue');
        }
        $request->validate([
            'payment_method' => 'required',
        ]);

        DB::beginTransaction();

        try {

            // 1️⃣ User handle
            if (auth()->check()) {
                $user = auth()->user();
            } else {
                $user = User::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'password' => bcrypt($request->password),
                ]);
                auth()->login($user);
            }
            if ($user) {
            $user_id = $user->id;
            $parent = Coa::findOrFail(7);
                $prefix = $parent->head_code;
                $maxCode = Coa::withTrashed()->where('parent_id', $parent->id)->max('head_code');
                if ($maxCode) {
                    $next = str_pad((int) substr($maxCode, strlen($prefix)) + 1, 2, '0', STR_PAD_LEFT);
                    $headCode = $prefix . $next;
                } else {
                    $headCode = $prefix . '01';
                }
                $account = Coa::create([
                    'parent_id'   => $parent->id,
                    'head_code'   => $headCode,
                    'head_name'   => $parent->head_name,
                    'transaction' => true,
                    'general'     => false,
                    'head_type'   => $parent->head_type,
                    'status'      => true,
                    'updateable'     => false,
                    'created_by'  => Auth::id(),
                ]);
                $client=Client::where('user_id',$user_id)->first();
                if(!$client){
                    Client::create([
                        'user_id'  => $user_id,
                        'region_id' => $request->region_id,
                        'area_id' => $request->area_id,
                        'territory_id' => $request->territory_id,
                        'coa_id' => $account->id,
                        'code' => $request->code,
                        'name' => $request->name,
                        'contact_person' => $request->contact_person,
                        'phone' => $request->phone,
                        'email' => $request->phone.'@email.com',
                        'address' => $request->address,
                        'bin_no' => $request->bin_no,
                        'credit_limit' => $request->credit_limit,
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            $cart = session('cart');
           
            
            // 2️⃣ Order create (NOW WITH TOTALS)
            $order = Order::create([
                'user_id'        => $user->id,
                'order_number'   => 'ORD-' . time(),
                'subtotal'       => $request->subtotal,
                'discount'       => $request->discount ?? 0,
                'tax'            => $request->tax ?? 0,
                'total'          => $request->total,
                'payment_method' => $request->payment_method,
                'status'         => 'pending',
                'note'       => $request->note,
            ]);

            $cart = session()->get('cart', []);
            $sizes = $request->size ?? [];
            foreach ($cart as $key => &$item) {
                if (isset($sizes[$key])) {
                    $item['size'] = $sizes[$key];
                }
            }
            unset($item);


            // 3️⃣ Order items + stock reduce
            foreach ($cart as $item) {
                    OrderItem::create([
                        'order_id'           => $order->id,
                        'product_id'         => $item['id'],
                        'product_variant_id' => $item['variant_id'] ?? null,
                        'qty'                => $item['qty'],
                        'price'              => $item['price'],
                        'size'               => $item['size'] ?? null,
                        'total'              => $item['price'] * $item['qty'],
                    ]);
                // Variant stock reduce
                if (!empty($item['variant_id']) || !empty($item['id'])) {
                    ProductVariant::where('id', $item['variant_id'])->orWhere('product_id', $item['id'])
                        ->decrement('stock', $item['qty']);
                }
            }

            session()->forget('cart');

            DB::commit();

            return redirect()->route('order.success')
                ->with('success', 'Order placed successfully');

        } catch (\Exception $e) { dd($e->getMessage());
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }



}