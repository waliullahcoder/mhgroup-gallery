<?php

use App\Http\Controllers\Frontend\ViewController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\UserController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', [ViewController::class, 'index'])->name('home');

//Login Register
Route::get('/signin', [ViewController::class, 'signinPage'])->name('auth.signinPage');
Route::get('/home-section-load/{type}', [ViewController::class, 'loadHomeSection'])
    ->name('home.section.load');
Route::get('/login', function () {
    return redirect()->route('auth.signinPage');
})->name('login');
Route::get('/signup', [ViewController::class, 'signupPage'])->name('auth.signupPage');

Route::post('/signin', [UserController::class, 'signinPost'])->name('user.signinPost');
Route::post('/signup', [UserController::class, 'signupPost'])->name('user.signupPost');
Route::get('/forgot-password', [ViewController::class, 'forgotPasswordPage'])->name('auth.forgotPasswordPage');
Route::post('/forgot-password', [UserController::class, 'forgotPasswordPost'])->name('user.forgotPasswordPost');

Route::get('/info/{id}', [ViewController::class, 'infoPage'])->name('info.page');
/*
|--------------------------------------------------------------------------
| Cart Routes
|--------------------------------------------------------------------------
*/

Route::post('/add-to-cart', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update', [CartController::class,'update'])->name('cart.update');
Route::post('/cart/clear', [CartController::class,'clear'])->name('cart.clear');

/*
|--------------------------------------------------------------------------
| Checkout Routes
|--------------------------------------------------------------------------
*/
Route::get('/checkout', [CheckoutController::class, 'checkout'])
    ->name('checkout');
Route::post('/buy-now', [CheckoutController::class, 'buyNow'])->name('cart.buyNow');
Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])
    ->name('checkout.placeOrder');

/*
|--------------------------------------------------------------------------
| Order Routes
|--------------------------------------------------------------------------
*/
Route::get('/order/success', [OrderController::class, 'success'])
    ->name('order.success');

Route::middleware('auth')->group(function () {
    // User order history
    Route::get('/my-orders', [OrderController::class, 'index'])
        ->name('orders.index');
    Route::get('/my-orders/{order}', [OrderController::class, 'show'])
        ->name('orders.show');
    Route::get('/orders/{order}/track', [OrderController::class, 'track'])
        ->name('orders.track');
    Route::get('/my-orders/{order}/invoice', [OrderController::class, 'invoice'])
        ->name('orders.invoice');
});

// ----------Review and rating---------
Route::middleware('auth')->group(function () {

Route::post('/product/{product}/review',[ReviewController::class, 'store']
)->middleware('auth')->name('review.store');

});



/* ---------- USER DASHBOARD (Protected) ---------- */
Route::middleware(['auth'])->group(function () {
    Route::get('/user/dashboard', [UserController::class, 'dashboard'])
        ->name('frontend.user.dashboard');
    Route::get('/user/profile/edit', [UserController::class, 'updateEditProfile'])
    ->name('user.profile.edit');
    Route::post('/user/profile/update', [UserController::class, 'updateProfile'])
    ->name('user.profile.update');
    Route::get('/user/invoice/history', [UserController::class, 'invoiceHistory'])
        ->name('frontend.user.invoiceHistory');
    Route::get('/user/invoice/{invoice}', [UserController::class, 'salesInvoice'])
        ->name('frontend.user.salesInvoice');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});

//Wishlist
Route::middleware('auth')->group(function () {

    Route::get('/wishlist', [WishlistController::class, 'index'])
        ->name('wishlist.index');

    Route::post('/wishlist/{product}', [WishlistController::class, 'store'])
        ->name('wishlist.store');

    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])
        ->name('wishlist.destroy');

});

Route::get('/products/search', [ViewController::class, 'search'])->name('products.search');
//Categories
Route::get('/category/{id}/{slug}/{menu}', [ViewController::class, 'categoryPage'])->name('category.index');
Route::get('/signle/sub/category/{id}', [ViewController::class, 'singleCategoryPage'])->name('category.singleCategoryPage');

//Products
Route::get('/product/details/{id}', [ViewController::class, 'productDetails'])->name('product.details');

Route::get('/filter-products', [ViewController::class, 'filterProducts'])
    ->name('filter.products');
Route::get('/filter-sub-products', [ViewController::class, 'subFilterProducts'])
    ->name('filter.sub.products');




















Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('clear-compiled');
    return back()->withSuccessMessage('Cache Cleared Successfully!');
})->name('admin.cache.clear');


Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return back()->withSuccessMessage('Storage linked successfully!');
})->name('admin.storage.link');

Route::get('/toggle-debug', function () {
    $envPath = base_path('.env');

    if (!file_exists($envPath)) {
        return back()->with('errorMessage', '.env file not found.');
    }

    $envContent = file_get_contents($envPath);

    if (preg_match('/APP_DEBUG=(true|false)/', $envContent, $matches)) {
        $currentStatus = $matches[1];
        $newStatus = $currentStatus === 'true' ? 'false' : 'true';

        $updatedEnv = preg_replace('/APP_DEBUG=(true|false)/', "APP_DEBUG={$newStatus}", $envContent);

        file_put_contents($envPath, $updatedEnv);
        Artisan::call('config:clear');

        return back()->withSuccessMessage("APP_DEBUG changed to {$newStatus} successfully!");
    }

    return back()->withErrors('APP_DEBUG not found in .env.');
})->name('admin.toggle.debug');

Route::get('/down', function (Request $request) {
    if ($request->query('token') !== env('MAINTENANCE_TOKEN')) {
        abort(403);
    }

    Artisan::call('down', [
        '--secret' => '/my-secret-up' // Allows access to /my-secret-up when app is down
    ]);
    return back();
});

Route::get('/up', function (Request $request) {
    if ($request->query('token') !== env('MAINTENANCE_TOKEN')) {
        abort(403);
    }

    Artisan::call('up');
    return redirect()->route('home');
});
