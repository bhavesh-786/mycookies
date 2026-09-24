<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Order;

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
        // Share pending orders count with all admin views
        View::composer('admin.*', function ($view) {
            $pendingOrdersCount = Order::where('status', 'pending')->count();
            $view->with('pendingOrdersCount', $pendingOrdersCount);
        });
    }
}
