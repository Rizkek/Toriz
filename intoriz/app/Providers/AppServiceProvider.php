<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Product;

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
        // Share notifications data with all views (or specific layout)
        View::composer('layouts.app', function ($view) {
            $lowStockCount = Product::lowStock()->active()->count();
            $expiringCount = Product::expiringSoon(30)->active()->count();

            $notifications = [];
            if ($lowStockCount > 0) {
                $notifications[] = [
                    'icon' => 'warning',
                    'color' => 'text-orange-400',
                    'message' => "$lowStockCount products low on stock",
                    'route' => route('reports.low-stock')
                ];
            }
            if ($expiringCount > 0) {
                $notifications[] = [
                    'icon' => 'event_busy',
                    'color' => 'text-red-400',
                    'message' => "$expiringCount products expiring soon",
                    'route' => route('reports.expiry')
                ];
            }

            $view->with('globalNotifications', $notifications);
        });
    }
}
