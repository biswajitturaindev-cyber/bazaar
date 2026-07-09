<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\KycDetail;
use Illuminate\Support\Facades\View;

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
        View::composer('*', function ($view) {
            $kycNotifications = KycDetail::with('business.user')
                ->whereNull('updated_at')
                ->latest()
                ->get();

            $view->with([
                'kycNotifications' => $kycNotifications,
                'kycNotificationCount' => $kycNotifications->count(),
            ]);
        });
    }
}
