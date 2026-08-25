<?php

namespace App\Providers;

use App\Support\Architecture263Api;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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
        Schema::defaultStringLength(191);

        View::composer('layouts.wizard', function ($view) {
            $token = request()->cookie('portal_token');
            $portalUser = null;

            if ($token) {
                try {
                    $response = app(Architecture263Api::class)->getUser($token);
                    if ($response->successful()) {
                        $portalUser = $response->json();
                    }
                } catch (\Exception $e) {
                    // Admin backend unreachable — nav just falls back to logged-out state.
                }
            }

            $view->with('portalUser', $portalUser);
        });
    }
}
