<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use App\Services\FrontEndService;

class WishlistController extends Controller
{

    protected $frontEndService;
    public function __construct(FrontEndService $frontEndService)
    {
        $this->frontEndService = $frontEndService;
    }
    /* =====================
     * Wishlist Page
     ===================== */
    public function index()
    {
        $wishlists = Wishlist::with('product')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
        $menus = $this->frontEndService->getMenu();

        return view('frontend.user.wishlist', compact('wishlists', 'menus'));
    }

    /* =====================
     * Add to Wishlist
     ===================== */
    public function store($product_id)
    {
        Wishlist::firstOrCreate([
            'user_id'    => Auth::id(),
            'product_id' => $product_id,
        ]);

        return back()->with('success', 'Added to wishlist ❤️');
    }

    /* =====================
     * Remove from Wishlist
     ===================== */
    public function destroy($id)
    {
        Wishlist::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return back()->with('success', 'Removed from wishlist');
    }
}
