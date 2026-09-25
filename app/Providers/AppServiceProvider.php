<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\AdminMenu;
use App\Models\AdminSetting;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        view()->composer('*', function ($view) {
            $admin_menus = Cache::remember('admin_menus', 3600, function () {
                return AdminMenu::root()->with([
                    'children' => function ($q) {
                        $q->where('status', 1)->orderBy('order', 'asc');
                    },
                    'actions',
                ])->where('status', 1)->orderBy('order', 'asc')->get();
            });
            $admin_setting = Cache::remember('admin_setting', 3600, function () {
                return AdminSetting::first();
            });
             
           $view->with(
                'categories',
                Category::whereNull('parent_id')
                    ->where('status', 1)
                    ->get()
            );
             $view->with(
                'categories_prod',
                Category::with(['children'])
                                        ->whereNull('parent_id')
                                        ->whereIn('position', ['mega_menu_parent', 'header', 'homepage'])
                                        ->where('status', 1)
                                        ->orderBy('name', 'asc')
                                        ->get()
            );

            $view->with(
                'sub_categories',
                Category::whereNotNull('parent_id')
                    ->where('status', 1)
                    ->get()
                    ->groupBy('parent_id')
            );
     
            $view->with(
                'subbcategories',
                Category::whereNotNull('parent_id')
                    ->where('status', 1)
                    ->get()
            );

            $newOrders = Order::where('status', 'pending')->latest()->get();
            $newOrdersCount = $newOrders->count();
            $view->with(['newOrders' => $newOrders, 'newOrdersCount' => $newOrdersCount]);
            $settings = Cache::remember('setting', 3600, function () {
                return Setting::first();
            });
            $view->with(['admin_menus' => $admin_menus, 'admin_setting' => $admin_setting, 'settings' => $settings]);
        });
    }
}
