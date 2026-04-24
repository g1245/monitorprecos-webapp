<?php

namespace App\Providers;

use Livewire\Livewire;
use App\Models\Product;
use App\Models\Store;
use App\Models\Department;
use App\Observers\DepartmentObserver;
use App\Observers\ProductObserver;
use App\Observers\StoreObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment(['production', 'staging'])) {
            URL::forceScheme('https');
        }

        // Configure rate limiters
        $this->configureRateLimiters();

        // Register model observers
        Product::observe(ProductObserver::class);
        Store::observe(StoreObserver::class);
        Department::observe(DepartmentObserver::class);

        // Share department menu data with all views
        View::share('departmentMenu', $this->getDepartmentMenuData());

        // Registrar componentes Livewire manualmente
        Livewire::component('department-products', \App\Livewire\DepartmentProducts::class);

        // Use Tailwind para paginação
        Paginator::defaultView('pagination::tailwind');
    }

    /**
     * Configure named rate limiters for sensitive endpoints.
     */
    private function configureRateLimiters(): void
    {
        // Login: 5 attempts per minute per IP
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Register: 3 new accounts per hour per IP
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        // Password recovery: 5 emails per hour per IP
        RateLimiter::for('password-recovery', function (Request $request) {
            return Limit::perHour(5)->by($request->ip());
        });

        // Newsletter: 3 subscriptions per hour per IP
        RateLimiter::for('newsletter', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        // Contact message: 5 messages per hour per IP
        RateLimiter::for('contact-message', function (Request $request) {
            return Limit::perHour(5)->by($request->ip());
        });
    }

    /**
     * Get department menu data from database with 5-minute caching.
     * Only returns departments that have at least one product attached,
     * either directly or through a child department.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getDepartmentMenuData()
    {
        try {
            // Cache department menu data for 300 seconds (5 minutes)
            return Cache::remember('department_menu', 300, function () {
                return Department::whereNull('parent_id')
                    ->where('show_in_menu', true)
                    ->where(function ($query) {
                        // Root department has products directly
                        $query->whereHas('products')
                            // OR at least one child has products
                            ->orWhereHas('children', function ($q) {
                                $q->where('show_in_menu', true)->whereHas('products');
                            });
                    })
                    ->with(['children' => function ($query) {
                        $query->where('show_in_menu', true)
                            ->whereHas('products')
                            ->orderBy('name', 'asc');
                    }])
                    ->orderBy('name', 'asc')
                    ->get();
            });
        } catch (\Exception $e) {
            Log::error('Error loading department menu: ' . $e->getMessage());
            return collect([]);
        }
    }
}
