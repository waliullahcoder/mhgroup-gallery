<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coa;
use App\Models\User;
use App\Models\Store;
use App\Models\Client;
use App\Models\Sales;
use App\Models\SalesList;
use App\Models\Product;
use App\Models\SalesOfficer;
use App\Models\ProductEdition;
use App\Models\Collection;
use App\Models\CollectionList;
use App\Models\AccountTransactionAuto;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\FrontEndService;
use Carbon\Carbon;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminOrderController extends Controller
{
    protected $adminService;

    public function __construct(FrontEndService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * 📦 Order List (Admin – All Orders)
     */
    public function index(Request $request)
    {
        $query = Order::query();

        // 🔍 Search by order number or status
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('status', 'like', '%' . $request->search . '%');
            });
        }

       $orders = $query->latest()->paginate(10)->withQueryString();

        return view('admin.order.orderList', compact('orders'));
    }

    /**
     * 🔄 Update Order Status (Admin)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        if ($request->status == 'pending') {
            return redirect()->back()->withErrors('Ops! Status is already Exist!');
        }
        if ($order->status == $request->status) {
            return redirect()->back()->withErrors('Ops! Status is already Exist!');
        }

         if ($request->status == 'cancelled') {
            // Restore stock
            foreach ($order->items as $item) {
                ProductVariant::where('id', $item->product_variant_id)->orWhere('product_id', $item->product_id)->increment('stock', $item->qty);
            }
        }else{
            if($order->status == 'pending'){
                try {
                    DB::transaction(function () use ($request,$order) {
                      
                        $client = Client::where('user_id', $order->user_id)->first();
                        $store = Store::first();
                        $sales_officer = SalesOfficer::where('email', 'online@gmail.com')->first();
                        $data = Sales::create([
                            'client_id' => $client->id,
                            'store_id'  => $store ? $store->id : 1,
                            'sales_officer_id' => $sales_officer? $sales_officer->id : 1,
                            'coa_id'    => $client->coa->id,
                            'sale_type' => 'Credit',
                            'invoice'   => $this->invoiceNo(),
                            'date'      => date('Y-m-d', strtotime($order->created_at)),
                            'amount'    => $order->subtotal,
                            'discount'  => $order->discount,
                            'net_amount' => $order->total,
                            'paid'      => 0.00,
                            'remarks'   => 'Online Order',
                            'created_by' => Auth::id(),
                        ]);

                        foreach ($order->items as $item) {
                            $product= Product::find($item->product_id);
                       $product_edition = ProductEdition::where('product_id', $item->product_id)->first();
                              if (!$product_edition) {
                                    throw new \Exception('CODE'.$item->product_id.',Please set Edition on Book!');
                                }
                            SalesList::create([
                                'sales_id' => $data->id,
                                'store_id' => $store ? $store->id : 1,
                                'client_id' => $client->id,
                                'product_id' => $item->product_id,
                                'product_edition_id' => $product_edition->id,
                                'price' => $item->price,
                                'commission' => 0,
                                'commission_amount' => 0,
                                'rate' => $product->sale_price,
                                'qty' => $item->qty,
                                'amount' => $item->total,
                                'discount' => $order->discount,
                                'net_amount' => $item->total - $order->discount,
                            ]);
                        }

                        if ($client->coa) {
                            $income_head = Coa::where('head_type', 'I')->where('head_name', 'Product Sales')->first();
                            $headCode = collect([
                                '0' => $client->coa->head_code,
                                '1' => $income_head->head_code,
                            ]);

                            $debit_amount = collect([
                                '0' => $request->net_amount,
                                '1' => 0.00
                            ]);

                            $credit_amount = collect([
                                '0' => 0.00,
                                '1' => $request->net_amount,
                            ]);

                            $countHead = count($headCode);
                            $postData = [];
                            for ($i = 0; $i < $countHead; $i++) {
                                $coa = Coa::where('head_code', $headCode[$i])->first();
                                $postData[] = [
                                    'voucher_no' => $data->invoice,
                                    'voucher_type' => "Client Sales",
                                    'date' => date('Y-m-d', strtotime($request->date)),
                                    'coa_id' => $coa->id,
                                    'coa_head_code' => $headCode[$i],
                                    'narration' => 'Client Sales Against Invoice No - ' . $data->invoice,
                                    'debit_amount' => $debit_amount[$i],
                                    'credit_amount' => $credit_amount[$i],
                                    'created_by' => Auth::id(),
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ];
                            }
                            AccountTransactionAuto::insert($postData);
                        }

                    });
                } catch (\Exception $e) {
                    dd($e);
                    return back()->withErrors($e->getMessage());
                }
            }


            $order->update([
                'status' => $request->status
            ]);

        }
        return redirect()->back()->withSuccessMessage('Order status updated successfully ✅');
    }

     /**
     * Generate Invoice No.
     */
    public function invoiceNo()
    {
        $data = Sales::withTrashed()->select(['invoice'])->whereDate('created_at', '>=', date('Y-m-01'))->whereDate('created_at', '<=', date('Y-m-t'))->orderBy('id', 'desc')->first();
        if ($data) {
            $trim = (int)str_replace("CS", '', $data->invoice) + 1;
            return "CS" . $trim;
        } else {
            return "CS" . date('ym') . '001';
        }
    }


    /**
     * 🚚 Track Order
     */
    public function track(Order $order)
    {
        return view('admin.order.track', compact('order'));
    }

    /**
     * 👁 Order Details
     */
    public function show(Order $order)
    {
        $order->load('items.product', 'items.productVariant');

        return view('admin.order.show', compact('order'));
    }

    /**
     * 🧾 Invoice View
     */
    public function invoice(Order $order)
    {
        $order->load('items.product', 'items.productVariant');

        // PDF needed later
        // $pdf = Pdf::loadView('admin.order.invoice', compact('order'));
        // return $pdf->download('invoice-' . $order->order_number . '.pdf');

        return view('admin.order.invoice', compact('order'));
    }

    /**
     * ✅ Order Success (Admin)
     */
    public function success()
    {
        $menus = $this->adminService->getMenu();

        $order = Order::with('items.product')
            ->latest()
            ->first();

        if (!$order) {
            return redirect('/');
        }

        return view('admin.order.success', compact('order', 'menus'))
            ->with('success', 'Order placed successfully');
    }
}
