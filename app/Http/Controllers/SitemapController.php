<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    private const CHUNK_SIZE = 50_000;
    private const CACHE_TTL  = 86_400; // 24 horas

    /**
     * Sitemap Index — lista todos os sub-sitemaps (departamentos + páginas de produtos).
     */
    public function index(): Response
    {
        $xml = Cache::remember('sitemap.index', self::CACHE_TTL, function () {
            $totalProducts = $this->activeProductsQuery()->count();
            $totalPages    = (int) ceil($totalProducts / self::CHUNK_SIZE);
            $totalPages    = max(1, $totalPages);

            $sitemaps = [];

            $sitemaps[] = [
                'loc'     => route('sitemap.departments'),
                'lastmod' => now()->toAtomString(),
            ];

            for ($page = 1; $page <= $totalPages; $page++) {
                $sitemaps[] = [
                    'loc'     => route('sitemap.products', ['page' => $page]),
                    'lastmod' => now()->toAtomString(),
                ];
            }

            return view('sitemap.index', compact('sitemaps'))->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Sub-sitemap de departamentos que possuem ao menos um produto ativo.
     */
    public function departments(): Response
    {
        $xml = Cache::remember('sitemap.departments', self::CACHE_TTL, function () {
            $departments = Department::query()
                ->select(['id', 'name'])
                ->whereHas('products', fn ($q) => $this->applyActiveProductScopes($q))
                ->orderBy('id')
                ->get();

            return view('sitemap.departments', compact('departments'))->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Sub-sitemap de produtos ativos — paginado em blocos de 50.000.
     */
    public function products(int $page): Response
    {
        $totalProducts = Cache::remember('sitemap.products_count', self::CACHE_TTL, function () {
            return $this->activeProductsQuery()->count();
        });

        $totalPages = max(1, (int) ceil($totalProducts / self::CHUNK_SIZE));

        if ($page < 1 || $page > $totalPages) {
            abort(404);
        }

        $xml = Cache::remember("sitemap.products.{$page}", self::CACHE_TTL, function () use ($page) {
            $offset = ($page - 1) * self::CHUNK_SIZE;

            $products = $this->activeProductsQuery()
                ->select(['id', 'name', 'updated_at'])
                ->orderBy('id')
                ->offset($offset)
                ->limit(self::CHUNK_SIZE)
                ->get();

            return view('sitemap.products', compact('products'))->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Retorna a query base de produtos ativos (mesmos filtros das páginas de departamento).
     *
     * @return \Illuminate\Database\Eloquent\Builder<Product>
     */
    private function activeProductsQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Product::query()
            ->fromPublicStore()
            ->parentProducts()
            ->inStock();
    }

    /**
     * Aplica os escopos de produto ativo em uma query já iniciada (usada em whereHas).
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Product>  $query
     * @return \Illuminate\Database\Eloquent\Builder<Product>
     */
    private function applyActiveProductScopes(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query
            ->fromPublicStore()
            ->parentProducts()
            ->inStock();
    }
}
