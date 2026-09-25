<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Slider;
use App\Models\HomeSection;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Controllers\Controller;
use App\Services\FrontEndService;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    protected $frontEndService;
    public function __construct(FrontEndService $frontEndService)
    {
        $this->frontEndService = $frontEndService;
    }

    
        public function index(Request $request)
        {
            $query = Order::where('user_id', auth()->id());
            $menus = $this->frontEndService->getMenu();

            // 🔍 Search by order number or status
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('order_number', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%');
                });
            }

            $orders = $query->latest()->paginate(100)->withQueryString();

            return view('frontend.order.orderList', compact('orders', 'menus'));
        }

        public function track(Order $order)
        {
            if ($order->user_id !== auth()->id()) {
                abort(403);
            }
            
            $menus = $this->frontEndService->getMenu();
            return view('frontend.order.track', compact('order','menus'));
        }


           
        public function show(Order $order)
        {
            abort_if($order->user_id !== auth()->id(), 403);
            $menus = $this->frontEndService->getMenu();
            $order->load('items.product', 'items.productVariant');

            return view('frontend.order.show', compact('order', 'menus'));
        }

        public function invoice(Order $order)
        {
            abort_if($order->user_id !== auth()->id(), 403);

            $order->load('items.product', 'items.productVariant');

            // $pdf = Pdf::loadView('frontend.order.invoice', compact('order'));
            // return $pdf->download('invoice-' . $order->order_number . '.pdf');

            return view('frontend.order.invoice', compact('order'));
        }


     public function success()
     {
        $menus = $this->frontEndService->getMenu();
        $order = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->latest()
            ->first();

        if (!$order) {
            return redirect('/');
        }

        return view('frontend.order.success', compact('order','menus'))->with('success', 'Order placed successfully');
     }


}