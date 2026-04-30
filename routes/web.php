<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordRecoveryController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserWishProductController;
use App\Http\Controllers\NewsletterLeadController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\MarkdownFeedController;
use App\Http\Controllers\AwinWebhookController;

// AWIN transaction webhook — exempt from CSRF, secret-token protected
Route::post('/webhooks/awin/transactions', [AwinWebhookController::class, 'store'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('webhooks.awin.transactions');

// AI-readable Markdown feed (token-protected, no browsing history tracking)
Route::get('/destaques.md', [MarkdownFeedController::class, 'highlights'])->name('destaques.md');

// Short redirect alias for sharing (e.g. monitordeprecos.com.br/84485/r)
Route::get('/{id}/r', [ProductController::class, 'redirectToStore'])->name('product.redirect.short');

// Similar products JSON endpoint — no browsing history tracking
Route::get('/produto/{id}/similares', [ProductController::class, 'similar'])
    ->middleware('throttle:60,1')
    ->name('product.similar');

// Same category products JSON endpoint — ordered by highest discount
Route::get('/produto/{id}/mesma-categoria', [ProductController::class, 'sameCategory'])
    ->middleware('throttle:60,1')
    ->name('product.same-category');

// Share card — no-index, no tracking, intended for Playwright/Puppeteer screenshot
Route::get('/share/{id}', [ProductController::class, 'share'])->name('product.share');

// Public routes with browsing history tracking
Route::middleware(['web', 'track.browsing'])->group(function () {
    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
    Route::get('/recentes', [WelcomeController::class, 'index'])->name('welcome.recentes');
    Route::get('/mais-acessados', [WelcomeController::class, 'index'])->name('welcome.mais-acessados');

    Route::get('/categoria/{slug}', [PagesController::class, 'category'])->name('pages.category');
    
    Route::get('/{alias}/{departmentId}/dp', [DepartmentController::class, 'index'])->name('department.index');

    Route::get('/destaques', [DepartmentController::class, 'index'])
        ->defaults('alias', 'destaques')
        ->defaults('departmentId', 154)
        ->name('destaques.index');
        
    Route::get('/{id}/{slug}/p', [ProductController::class, 'index'])->name('product.show');
    Route::get('/share/whatsapp/{id}', [ProductController::class, 'shareWhatsapp'])->name('product.share.whatsapp');
    Route::get('/product/{id}/redirect', [ProductController::class, 'redirectToStore'])->name('product.redirect');
    
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    
    Route::get('/lojas', [StoreController::class, 'index'])->name('stores.index');
    Route::get('/{slug}/{id}/loja', [StoreController::class, 'show'])->name('store.show');
});

Route::get('/loja/{id}/logo', [StoreController::class, 'logo'])->name('store.logo');

// Newsletter subscription route
Route::post('/newsletter/subscribe', [NewsletterLeadController::class, 'store'])
    ->middleware('throttle:newsletter')
    ->name('newsletter.subscribe');

// Contact message route
Route::post('/contact-message', [ContactMessageController::class, 'store'])
    ->middleware('throttle:contact-message')
    ->name('contact-message.store');

// Authentication routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
        
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
        
        Route::get('/recovery', [PasswordRecoveryController::class, 'showForgotPassword'])->name('recovery');
        Route::post('/recovery', [PasswordRecoveryController::class, 'sendResetLink'])->middleware('throttle:password-recovery');
        
        Route::get('/reset-password/{token}', [PasswordRecoveryController::class, 'showResetPassword'])->name('password.reset');
        Route::post('/reset-password', [PasswordRecoveryController::class, 'resetPassword'])->name('password.update');
    });
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
});

// Account routes (requires authentication)
Route::prefix('account')->name('account.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [AccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/wishlist', [AccountController::class, 'wishlist'])->name('wishlist');
    Route::get('/price-alerts', [AccountController::class, 'priceAlerts'])->name('price-alerts');
    Route::get('/history', [AccountController::class, 'browsingHistory'])->name('history');
});

// User wish products routes (wishlist + price alerts)
Route::prefix('wish-products')->middleware('auth')->group(function () {
    Route::post('/', [UserWishProductController::class, 'store'])->name('wish-products.store');
    Route::delete('/{productId}', [UserWishProductController::class, 'destroy'])->name('wish-products.destroy');
    Route::get('/{productId}/check', [UserWishProductController::class, 'check'])->name('wish-products.check');
    Route::patch('/{productId}/price-alert', [UserWishProductController::class, 'updatePriceAlert'])->name('wish-products.update-alert');
    Route::post('/{productId}/toggle-alert', [UserWishProductController::class, 'toggleAlert'])->name('wish-products.toggle-alert');
});

// Static pages
Route::get('/sobre-nos', [PagesController::class, 'show'])->defaults('slug', 'sobre-nos')->name('pages.about');
Route::get('/como-funciona', [PagesController::class, 'show'])->defaults('slug', 'como-funciona')->name('pages.how');
Route::get('/central-de-ajuda', [PagesController::class, 'show'])->defaults('slug', 'central-de-ajuda')->name('pages.help-center');
Route::get('/politica-de-privacidade', [PagesController::class, 'show'])->defaults('slug', 'politica-de-privacidade')->name('pages.privacy');
Route::get('/termos-de-uso', [PagesController::class, 'show'])->defaults('slug', 'termos-de-uso')->name('pages.terms');

// WhatsApp group landing page
Route::view('/grupo', 'pages.grupo')->name('pages.grupo');

// WhatsApp group redirect (fires Lead + ViewContent pixels before redirecting)
Route::view('/grupo/entrar', 'pages.grupo-redirect')->name('pages.grupo.redirect');

