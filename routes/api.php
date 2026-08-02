<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MerchantController;
use App\Http\Controllers\Api\RiderController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\MasterProductController;
use App\Http\Controllers\Api\WaitlistController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Top-level alias to satisfy requirement
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/migrate', function () {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return \Illuminate\Support\Facades\Artisan::output();
    });

    // Public routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/social-login', [AuthController::class, 'socialLogin']);
    Route::post('/auth/social-complete', [AuthController::class, 'completeSocialRegistration']);
    Route::post('/auth/otp/send', [AuthController::class, 'sendOtp']);
    Route::post('/auth/otp/verify', [AuthController::class, 'verifyOtp']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    Route::post('/waitlist', [WaitlistController::class, 'store']);

    // Payments Callback (Public)
    Route::post('/payments/callback', [TransactionController::class, 'paymentCallback'])->name('payments.callback');

    // Public browsing
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::get('/master-products', [MasterProductController::class, 'index']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // User management
        Route::apiResource('/users', UserController::class);
        Route::put('/users/{user}/location', [UserController::class, 'updateLocation']);
        Route::put('/users/{user}/fcm-token', [UserController::class, 'updateFcmToken']);

        // Addresses
        Route::apiResource('/addresses', AddressController::class);
        Route::put('/addresses/{address}/default', [AddressController::class, 'setDefault']);

        // FCM Token update
        Route::post('/update-fcm-token', function (Request $request) {
            $request->validate(['fcm_token' => 'required|string']);
            $request->user()->update(['fcm_token' => $request->fcm_token]);
            return response()->json(['message' => 'Token updated successfully']);
        });

        // Merchant routes
        Route::middleware('role:merchant')->prefix('merchant')->group(function () {
            Route::apiResource('/products', ProductController::class)->except(['index', 'show']);
            Route::get('/products', [ProductController::class, 'merchantProducts']);
            Route::get('/dashboard', [MerchantController::class, 'dashboard']);
            Route::get('/orders', [MerchantController::class, 'orders']);
            Route::put('/orders/{order}/status', [MerchantController::class, 'updateOrderStatus']);
            Route::post('/location', [MerchantController::class, 'updateLocation']);
        });

        // Rider routes
        Route::middleware('role:rider')->prefix('rider')->group(function () {
            Route::get('/orders', [RiderController::class, 'riderOrders']);
            Route::get('/profile', [RiderController::class, 'profile']);
            Route::post('/location', [RiderController::class, 'updateLocation']);
            Route::post('/online', [RiderController::class, 'goOnline']);
            Route::post('/offline', [RiderController::class, 'goOffline']);
            Route::get('/available-orders', [RiderController::class, 'availableOrders']);
            Route::post('/orders/{order}/accept', [RiderController::class, 'acceptOrder']);
            Route::put('/orders/{order}/status', [RiderController::class, 'updateOrderStatus']);
            Route::get('/earnings', [RiderController::class, 'earnings']);
            Route::post('/payout/request', [RiderController::class, 'requestPayout']);
        });

        // Customer routes
        Route::middleware('role:customer')->prefix('customer')->group(function () {
            Route::post('/orders', [OrderController::class, 'store']);
            Route::get('/orders', [OrderController::class, 'customerOrders']);
            Route::get('/orders/{order}', [OrderController::class, 'show']);
        });

        // Orders (general)
        Route::get('/orders/{order}/tracking', [OrderController::class, 'tracking']);
        Route::put('/orders/{order}/cancel', [OrderController::class, 'cancel']);

        // Wallet
        Route::get('/wallet', [WalletController::class, 'show']);
        Route::get('/transactions', [TransactionController::class, 'index']);

        // Payments
        Route::post('/payments/initiate', [TransactionController::class, 'initiatePayment']);
        Route::get('/payments/{order}/status', [TransactionController::class, 'checkStatus']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::put('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
        // Fix typo from previous version
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    });

    // Upload routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/uploads/image', [UploadController::class, 'uploadImage']);
        Route::post('/uploads/images', [UploadController::class, 'uploadMultipleImages']);
    });

    // Admin routes
    Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/expansion-metrics', [AdminController::class, 'expansionMetrics']);
        Route::get('/financial-ledger', [AdminController::class, 'financialReconciliation']);
        Route::get('/sales-analytics', [AdminController::class, 'salesAnalytics']);
        Route::get('/vendor-performance', [AdminController::class, 'vendorPerformance']);
        Route::get('/delivery-logistics', [AdminController::class, 'deliveryLogistics']);
        Route::post('/delivery-pricing', [AdminController::class, 'updateDeliveryPricing']);
        Route::get('/settings', [AdminController::class, 'systemSettings']);

        Route::get('/orders', [AdminController::class, 'orders']);
        Route::get('/merchants', [AdminController::class, 'merchants']);
        Route::get('/riders', [AdminController::class, 'riders']);
        Route::post('/riders', [AdminController::class, 'storeRider']);
        Route::get('/transactions', [AdminController::class, 'transactions']);
        Route::post('/merchants/{merchant}/verify', [AdminController::class, 'verifyMerchant']);
        Route::post('/riders/{rider}/verify', [AdminController::class, 'verifyRider']);
    });
});
