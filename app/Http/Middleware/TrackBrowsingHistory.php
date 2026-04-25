<?php

namespace App\Http\Middleware;

use App\Jobs\TrackBrowsingHistoryJob;
use App\Services\BotDetectionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackBrowsingHistory
{
    public function __construct(
        protected BotDetectionService $botDetector
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track successful GET requests
        if ($request->isMethod('GET') && $response->isSuccessful()) {
            $this->trackVisit($request);
        }

        return $response;
    }

    /**
     * Track the page visit.
     */
    protected function trackVisit(Request $request): void
    {
        if ($this->botDetector->isBot($request->userAgent())) {
            return;
        }

        $route = $request->route();
        
        if (!$route) {
            return;
        }

        $routeName = $route->getName();
        $pageType = $this->determinePageType($routeName);

        if (!$pageType) {
            return; // Don't track certain pages like assets, etc.
        }

        $data = [
            'user_id' => Auth::id(),
            'page_type' => $pageType,
            'page_url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'visited_at' => now(),
        ];

        // Add specific IDs based on route
        if ($routeName === 'product.show') {
            $data['product_id'] = $route->parameter('id');
        } elseif ($routeName === 'department.index') {
            $data['department_id'] = $route->parameter('departmentId');
        } elseif ($routeName === 'store.show') {
            $data['store_id'] = $route->parameter('id');
        }

        TrackBrowsingHistoryJob::dispatch($data);
    }

    /**
     * Determine the page type based on route name.
     */
    protected function determinePageType(?string $routeName): ?string
    {
        if (!$routeName) {
            return null;
        }

        return match (true) {
            $routeName === 'welcome' => 'home',
            str_starts_with($routeName, 'product.') => 'product',
            str_starts_with($routeName, 'department.') => 'department',
            str_starts_with($routeName, 'store.') => 'store',
            str_starts_with($routeName, 'search.') => 'search',
            default => null,
        };
    }
}
