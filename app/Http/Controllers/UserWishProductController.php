<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UserWishProduct;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserWishProductController extends Controller
{
    public function __construct(
        private readonly AdminNotificationService $adminNotificationService
    ) {}
    /**
     * Add a product to user's wishlist (with optional price alert).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'target_price' => ['nullable', 'numeric', 'min:0', 'max:1000000000'],
        ]);

        $user = Auth::user();
        $productId = $validated['product_id'];

        // Use updateOrCreate to prevent duplicate entry errors
        $wish = UserWishProduct::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $productId,
            ],
            [
                'target_price' => $validated['target_price'] ?? null,
                'is_active' => true,
            ]
        );

        $isNew = $wish->wasRecentlyCreated;

        if ($isNew && $wish->hasPriceAlert()) {
            $this->adminNotificationService->notifyNewPriceAlert($wish);
        }

        $message = match(true) {
            $isNew && $wish->hasPriceAlert() => 'Produto salvo com alerta de preço!',
            $isNew => 'Produto adicionado à sua lista de desejos!',
            $wish->hasPriceAlert() => 'Produto salvo e alerta de preço atualizado!',
            default => 'Produto atualizado na sua lista de desejos',
        };

        return response()->json([
            'message' => $message,
            'wished' => true,
            'has_alert' => $wish->hasPriceAlert(),
            'wish' => $wish,
        ], $isNew ? 201 : 200);
    }

    /**
     * Remove a product from wishlist.
     */
    public function destroy(int $productId)
    {
        $user = Auth::user();

        $wish = UserWishProduct::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if (!$wish) {
            return response()->json([
                'message' => 'Produto não encontrado na lista de desejos',
                'wished' => false,
            ], 404);
        }

        $wish->delete();

        return response()->json([
            'message' => 'Produto removido com sucesso',
            'wished' => false,
        ], 200);
    }

    /**
     * Check if a product is in wishlist and get details.
     */
    public function check(int $productId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'wished' => false,
                'has_alert' => false,
            ]);
        }

        $wish = UserWishProduct::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        return response()->json([
            'wished' => $wish !== null,
            'has_alert' => $wish ? $wish->hasPriceAlert() : false,
            'wish' => $wish,
        ]);
    }

    /**
     * Update only the price alert for a wished product.
     */
    public function updatePriceAlert(Request $request, int $productId)
    {
        $validated = $request->validate([
            'target_price' => ['nullable', 'numeric', 'min:0', 'max:1000000000'],
        ]);

        $user = Auth::user();

        $wish = UserWishProduct::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if (!$wish) {
            return response()->json([
                'message' => 'Produto não está na sua lista de desejos',
            ], 404);
        }

        $wish->update([
            'target_price' => $validated['target_price'] ?? null,
            'is_active' => $validated['target_price'] !== null,
        ]);

        return response()->json([
            'message' => $wish->hasPriceAlert() 
                ? 'Alerta de preço atualizado!' 
                : 'Alerta de preço removido',
            'wish' => $wish,
        ]);
    }

    /**
     * Toggle price alert active status.
     */
    public function toggleAlert(int $productId)
    {
        $user = Auth::user();

        $wish = UserWishProduct::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if (!$wish) {
            return response()->json([
                'message' => 'Produto não está na sua lista de desejos',
            ], 404);
        }

        if (!$wish->hasPriceAlert()) {
            return response()->json([
                'message' => 'Este produto não tem alerta de preço configurado',
            ], 400);
        }

        $wish->update(['is_active' => !$wish->is_active]);

        return response()->json([
            'message' => $wish->is_active ? 'Alerta ativado' : 'Alerta desativado',
            'wish' => $wish,
        ]);
    }
}
