<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display the specified product with price comparison.
     */
    public function index(int $id, string $slug, Request $request)
    {
        $product = Product::query()
            ->with(['departments', 'attributes'])
            ->where('products.id', $id)
            ->fromPublicStore()
            ->firstOrFail();

        // Get real price history data
        $priceHistory = $this->getPriceHistory($product);

        return view('product.index', [
            'product' => $product,
            'priceHistory' => $priceHistory,
            'slug' => $slug,
        ]);
    }

    /**
     * Render a 1:1 share card for screenshot via Playwright/Puppeteer.
     * Not indexed, no tracking, no interactions.
     */
    public function share(int $id)
    {
        $product = Product::query()
            ->with(['departments', 'attributes'])
            ->where('products.id', $id)
            ->fromPublicStore()
            ->firstOrFail();

        $priceHistory = $this->getPriceHistory($product);

        return response()
            ->view('product.share', compact('product', 'priceHistory'))
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    /**
     * Share product on WhatsApp.
     */
    public function shareWhatsapp(int $id)
    {
        $product = Product::findOrFail($id);

        $productUrl = route('product.show', ['id' => $product->id, 'slug' => Str::of($product->name)->slug()]);
        $text = urlencode($product->name . ' ' . $productUrl);

        return redirect("https://wa.me/?text=$text");
    }

    /**
     * Redirect to product deep link (external store).
     * This route is used to track clicks before redirecting to the store.
     */
    public function redirectToStore(int $id)
    {
        $product = Product::with('store')->findOrFail($id);

        // TODO: Add tracking logic here (e.g., log click event, update analytics)

        $utmParams = http_build_query([
            'utm_source'   => 'monitorprecos',
            'utm_medium'   => 'price_comparison',
            'utm_campaign' => Str::slug($product->store?->name ?? 'loja'),
            'utm_content'  => $product->id,
            'utm_term'     => Str::slug($product->name),
        ]);

        $separator = str_contains($product->deep_link, '?') ? '&' : '?';
        $url = $product->deep_link . $separator . $utmParams;

        return redirect($url)->header('X-Robots-Tag', 'noindex, nofollow');
    }

    /**
     * Get price history for the product.
     */
    private function getPriceHistory(Product $product): array
    {
        $histories = $product->priceHistories()
            ->orderBy('created_at', 'asc')
            ->get();

        if ($histories->isEmpty()) {
            return [
                'data' => [],
                'lowest_price' => null,
                'highest_price' => null,
                'current_price' => $product->price,
                'has_history' => false,
            ];
        }

        $data = $histories->map(function ($history) {
            return [
                'date' => $history->created_at->format('Y-m-d'),
                'price' => $history->price,
                'formatted_date' => $history->created_at->format('d/m')
            ];
        })->toArray();

        return [
            'data' => $data,
            'lowest_price' => $histories->min('price'),
            'highest_price' => $histories->max('price'),
            'current_price' => $product->price,
            'has_history' => true,
        ];
    }

    /**
     * Return similar products based on name, brand and store via Meilisearch.
     * Falls back to a broader search (without store_id) if fewer than 5 results are found.
     */
    public function similar(int $id): \Illuminate\Http\JsonResponse
    {
        $product = Product::query()
            ->fromPublicStore()
            ->findOrFail($id);

        $query = collect(explode(' ', $product->name))
            ->take(4)
            ->implode(' ');

        $results = Product::search($query)
            ->where('is_store_visible', true)
            ->where('is_parent', 0)
            ->when($product->brand, fn ($q) => $q->where('brand', $product->brand))
            ->where('store_id', $product->store_id)
            ->take(20)
            ->get()
            ->reject(fn ($p) => $p->id === $product->id)
            ->take(20);

        if ($results->count() < 5) {
            $results = Product::search($query)
                ->where('is_store_visible', true)
                ->where('is_parent', 0)
                ->when($product->brand, fn ($q) => $q->where('brand', $product->brand))
                ->take(20)
                ->get()
                ->reject(fn ($p) => $p->id === $product->id)
                ->take(20);
        }

        $data = $results->map(fn ($p) => [
            'id'                  => $p->id,
            'name'                => $p->name,
            'price'               => $p->price,
            'old_price'           => $p->old_price && $p->old_price > $p->price ? $p->old_price : null,
            'discount_percentage' => $p->discount_percentage,
            'image_url'           => $p->image_url,
            'brand'               => $p->brand,
            'store_name'          => $p->store?->name,
            'url'                 => route('product.show', ['id' => $p->id, 'slug' => $p->permalink]),
        ])->values();

        return response()->json(['data' => $data]);
    }

    /**
     * Generate mock price history for demonstration.
     */
    private function getMockPriceHistory(Product $product): array
    {
        $currentPrice = $product->price;
        $historicalPrices = [];

        // Generate 90 days of price history
        for ($i = 90; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $variance = rand(-500, 300);
            $price = max($currentPrice + $variance, $currentPrice * 0.7); // Don't go below 70% of current price

            $historicalPrices[] = [
                'date' => $date->format('Y-m-d'),
                'price' => $price,
                'formatted_date' => $date->format('d/m')
            ];
        }

        return [
            'data' => $historicalPrices,
            'lowest_price' => min(array_column($historicalPrices, 'price')),
            'highest_price' => max(array_column($historicalPrices, 'price')),
            'current_price' => $currentPrice,
        ];
    }
}
