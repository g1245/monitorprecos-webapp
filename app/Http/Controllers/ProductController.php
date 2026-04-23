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
