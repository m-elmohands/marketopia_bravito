<?php

use App\Http\Controllers\Api\AboutSectionController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\BlogPostController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\Deliveryman\OrderController as DeliverymanOrderController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\NavigationMenuController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PrivacyPolicyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ShippingReturnController;
use App\Http\Controllers\Api\TermsOfServiceController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'lang'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::group(['middleware' => 'auth:api', 'prefix' => 'profile'], function () {
            Route::get('/', [AuthController::class, 'profile'])->name('profile');
            Route::post('/update', [AuthController::class, 'updateProfile'])->name('updateProfile');
            Route::post('/delete', [AuthController::class, 'deleteProfile'])->name('deleteProfile');
            Route::delete('/', [AuthController::class, 'deleteProfile'])->name('profile.destroy');
        });
    });
    Route::get('/config', [ConfigController::class, 'getConfig']);

    Route::group(['prefix' => 'home'], function () {
        Route::get('/banners', [HomeController::class, 'getBanners'])->name('banners');
        Route::get('/brands', [HomeController::class, 'getBrands'])->name('brands');
        Route::get('/categories', [HomeController::class, 'popularCategories'])->name('popular.categories');
        Route::get('/products', [HomeController::class, 'getLatestProducts'])->name('latest.products');
    });
    Route::group(['prefix' => 'categories'], function () {
        Route::get('/parent', [CategoryController::class, 'getParentCategories'])->name('getParentCategories');
        Route::get('/children/{id}', [CategoryController::class, 'getChildrenCategory'])->name('getChildrenCategories');
    });
    Route::group(['prefix' => 'products'], function () {
        Route::get('/', [ProductController::class, 'index'])->name('getAllProducts');
        Route::get('/category/{categoryId}', [ProductController::class, 'getProductsFromCategoryAndChildren'])->name('getProductsFromCategoryAndChildren');
        Route::get('/{id}', [ProductController::class, 'getProduct']);
        Route::get('/details/{id}', [ProductController::class, 'show'])->name('productDetails');
    });
    Route::resource('carts', CartController::class)->middleware('auth:api');
    Route::get('/cart/delivery-fee', [CartController::class, 'getDeliveryFee'])->middleware('auth:api');
    Route::resource('addresses', AddressController::class, [
        'only' => [
            'index',
            'store',
            'update',
            'destroy',
        ],
    ])->middleware('auth:api');

    Route::resource('wishlists', WishlistController::class, [
        'only' => [
            'index',
            'store',
            'destroy',
        ],
    ])->middleware('auth:api');
    Route::get('/coupons/{code}', [CouponController::class, 'validateCoupon'])
        ->name('validateCoupon')->middleware('auth:api');

    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', [OrderController::class, 'index'])->middleware('auth:api');
        Route::post('/', [OrderController::class, 'store'])->middleware('auth:api');
        Route::get('/{id}', [OrderController::class, 'show'])->middleware('auth:api');
        Route::post('/{id}/receipt', [OrderController::class, 'uploadReceipt'])->middleware('auth:api');
    });

    Route::get('/payment-method', [PaymentController::class, 'payment_method']);
    // Paymob callbacks configured in the Paymob dashboard.
    Route::post('/payments/paymob/processed', [PaymentController::class, 'callback'])
        ->name('payments.paymob.processed');
    Route::get('/payments/paymob/response', [PaymentController::class, 'paymobResponse'])
        ->name('payments.paymob.response');

    // Backward-compatible callback used by existing Paymob configurations.
    Route::post('/payment/callback', [PaymentController::class, 'callback']);
    Route::post('/paypal/webhook', [PaymentController::class, 'paypalWebhook']);

    // PayPal
    Route::post('/paypal/create-order', [PaymentController::class, 'paypalCreateOrder']);
    Route::post('/paypal/capture', [PaymentController::class, 'paypalCapture']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/admin/save-token', [AuthController::class, 'saveFcmTokenToAdmin'])->name('admin.save-fcm-token');
    Route::group(['prefix' => 'deliveryman', 'middleware' => 'CheckDeliveryman'], function () {
        Route::get('/orders', [DeliverymanOrderController::class, 'index']);
        Route::post('/orders/status/{id}', [DeliverymanOrderController::class, 'changeStatus']);
    });

    // ========== CONTENT MANAGEMENT ROUTES ==========
    // About Sections
    Route::group(['prefix' => 'about-sections'], function () {
        Route::get('/', [AboutSectionController::class, 'index']);
        Route::get('/{id}', [AboutSectionController::class, 'show']);
    });

    // Blog Posts
    Route::group(['prefix' => 'blog-posts'], function () {
        Route::get('/', [BlogPostController::class, 'index']);
        Route::get('/featured', [BlogPostController::class, 'featured']);
        Route::get('/category/{categoryId}', [BlogPostController::class, 'byCategory']);
        Route::get('/{slug}', [BlogPostController::class, 'show']);
    });

    // FAQs
    Route::group(['prefix' => 'faqs'], function () {
        Route::get('/', [FaqController::class, 'index']);
        Route::get('/categories', [FaqController::class, 'categories']);
        Route::get('/{id}', [FaqController::class, 'show']);
    });

    // Services
    Route::group(['prefix' => 'services'], function () {
        Route::get('/', [ServiceController::class, 'index']);
        Route::get('/{slug}', [ServiceController::class, 'show']);
    });

    // Testimonials
    Route::group(['prefix' => 'testimonials'], function () {
        Route::get('/', [TestimonialController::class, 'index']);
        Route::get('/featured', [TestimonialController::class, 'featured']);
        Route::get('/{id}', [TestimonialController::class, 'show']);
    });

    // ========== WEBSITE MANAGEMENT ROUTES ==========
    // Navigation Menus
    Route::group(['prefix' => 'navigation-menus'], function () {
        Route::get('/', [NavigationMenuController::class, 'index']);
        Route::get('/{location}', [NavigationMenuController::class, 'byLocation']);
    });

    // Shipping & Returns
    Route::group(['prefix' => 'shipping-returns'], function () {
        Route::get('/', [ShippingReturnController::class, 'index']);
        Route::get('/{slug}', [ShippingReturnController::class, 'show']);
    });

    // Privacy Policy
    Route::group(['prefix' => 'privacy-policies'], function () {
        Route::get('/', [PrivacyPolicyController::class, 'index']);
        Route::get('/{slug}', [PrivacyPolicyController::class, 'show']);
    });

    // Terms of Service
    Route::group(['prefix' => 'terms-of-services'], function () {
        Route::get('/', [TermsOfServiceController::class, 'index']);
        Route::get('/{slug}', [TermsOfServiceController::class, 'show']);
    });

    // ═══════════════════════════════════════════════════════════
    // SECTION 3: COMMUNICATIONS
    // ═══════════════════════════════════════════════════════════

    // Contact Messages - Public endpoint (no auth required)
    Route::post('/contact-messages', [ContactMessageController::class, 'store']);

    // ═══════════════════════════════════════════════════════════
    // SECTION 4: SYSTEM
    // ═══════════════════════════════════════════════════════════

    // Settings
    Route::group(['prefix' => 'settings'], function () {
        Route::get('/', [SettingController::class, 'index']);
        Route::get('/{key}', [SettingController::class, 'show']);
    });
});
