<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MarkdownFeedController extends Controller
{
    /**
     * Returns all "destaques" products (price below historical peak, sorted by
     * discount) in plain Markdown format — designed for AI content generation.
     *
     * Access is protected by a static token passed via the `token` query string
     * parameter. The token is stored in the MARKDOWN_FEED_TOKEN environment
     * variable and resolved through config('services.markdown_feed.token').
     */
    public function highlights(Request $request): Response
    {
        $configToken = (string) config('services.markdown_feed.token');

        if (
            empty($configToken)
            || ! $request->filled('token')
            || ! hash_equals($configToken, (string) $request->query('token'))
        ) {
            abort(401, 'Unauthorized');
        }

        $products = Product::query()
            ->fromPublicStore()
            ->parentProducts()
            ->whereColumn('price', '<', 'highest_recorded_price')
            ->orderByRaw('discount_percentage desc')
            ->select(['id', 'name', 'price', 'old_price', 'highest_recorded_price', 'description', 'image_url'])
            ->get();

        $baseUrl = rtrim((string) config('app.url'), '/');

        $lines = ['# Produtos em Destaque', ''];

        foreach ($products as $product) {
            $productUrl  = "{$baseUrl}/{$product->id}/{$product->permalink}/p";
            $redirectUrl = "{$baseUrl}/{$product->id}/r";

            $lines[] = '---';
            $lines[] = '';
            $lines[] = "## {$product->name}";
            $lines[] = '';
            $lines[] = "**Preço atual:** R$ {$product->price}";

            if (! empty($product->old_price)) {
                $lines[] = '';
                $lines[] = "**Preço anterior:** R$ {$product->old_price}";
            }

            if (! empty($product->highest_recorded_price)) {
                $lines[] = '';
                $lines[] = "**Maior preço registrado:** R$ {$product->highest_recorded_price}";
            }
            $lines[] = '';
            $lines[] = "**Descrição:** {$product->description}";
            $lines[] = '';
            $lines[] = "**Página do produto:** {$productUrl}";
            $lines[] = '';
            $lines[] = "**Link de compra:** {$redirectUrl}";

            if (! empty($product->image_url)) {
                $lines[] = '';
                $lines[] = "![{$product->name}]({$product->image_url})";
            }

            $lines[] = '';
        }

        $lines[] = '---';

        $markdown = implode("\n", $lines);

        return response($markdown, 200)
            ->header('Content-Type', 'text/markdown; charset=utf-8')
            ->header('X-Robots-Tag', 'noindex');
    }
}
