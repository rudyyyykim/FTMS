<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

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
         if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Load language routes
        if (file_exists(base_path('routes/lang.php'))) {
            Route::middleware('web')->group(base_path('routes/lang.php'));
        }

        // Share route prefix with all views
        view()->share('routePrefix', function($routeName) {
            $user = auth()->user();
            if (!$user) return 'admin.' . $routeName;

            $prefix = match($user->role) {
                'Pka' => 'pka.',
                'Admin' => 'admin.',
                default => 'admin.'
            };

            return $prefix . $routeName;
        });

        // Load language routes
        if (file_exists(base_path('routes/lang.php'))) {
            Route::middleware('web')->group(base_path('routes/lang.php'));
        }

        // Share route prefix with all views
        view()->share('routePrefix', function($routeName) {
            $user = auth()->user();
            if (!$user) return 'admin.' . $routeName;
            
            $prefix = match($user->role) {
                'Pka' => 'pka.',
                'Admin' => 'admin.',
                default => 'admin.'
            };
            
            return $prefix . $routeName;
        });
    }
}
