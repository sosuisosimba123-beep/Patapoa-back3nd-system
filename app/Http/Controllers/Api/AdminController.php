<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Rider;
use App\Models\Transaction;
use App\Models\Waitlist;
use App\Models\SearchLog;
use App\Models\DeliveryPricingRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\PushNotificationService;

class AdminController extends Controller
{
    protected $notifications;

    public function __construct(PushNotificationService $notifications)
    {
        $this->notifications = $notifications;
    }

    public function dashboard(Request $request)
    {
        // 1. Executive Metrics
        $totalOrders = Order::count();
        $completedOrdersCount = Order::where('status', 'delivered')->count();

        $dailyGMV = Order::whereDate('created_at', today())
            ->whereIn('payment_status', ['paid', 'completed'])
            ->sum('total');

        $totalRevenue = Transaction::where('type', 'payment')
            ->where('status', 'completed')
            ->sum('amount');

        $platformEarnings = Order::where('payment_status', 'paid')
            ->sum('platform_fee');

        $totalMerchants = Merchant::count();
        $activeSupermarkets = Merchant::where('is_verified', true)->where('is_online', true)->count();
        $totalRiders = Rider::count();
        $activeRiders = Rider::where('is_online', true)->count();

        $pendingMerchantVerifications = Merchant::where('is_verified', false)->count();
        $pendingRiderVerifications = Rider::where('is_verified', false)->count();

        $pendingPayouts = Transaction::where('type', 'payout')
            ->where('status', 'pending')
            ->sum('amount');

        return $this->successResponse([
            'executive' => [
                'total_orders' => $totalOrders,
                'completed_orders' => $completedOrdersCount,
                'daily_gmv' => (float)$dailyGMV,
                'total_revenue' => (float)$totalRevenue,
                'platform_earnings' => (float)$platformEarnings,
                'active_supermarkets' => $activeSupermarkets,
                'total_merchants' => $totalMerchants,
                'total_riders' => $totalRiders,
                'active_riders' => $activeRiders,
            ],
            'pending_actions' => [
                'merchant_verifications' => $pendingMerchantVerifications,
                'rider_verifications' => $pendingRiderVerifications,
                'payouts' => (float)$pendingPayouts,
            ]
        ], 'Dashboard data retrieved successfully');
    }

    public function financialReconciliation(Request $request)
    {
        $stats = Transaction::select(
                'type',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('count(*) as count')
            )
            ->where('status', 'completed')
            ->groupBy('type')
            ->get();

        $pendingSettlements = Transaction::where('type', 'payout')
            ->where('status', 'pending')
            ->sum('amount');

        $recentTransactions = Transaction::with(['user', 'order'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return $this->successResponse([
            'summary' => $stats,
            'pending_settlements' => (float)$pendingSettlements,
            'recent_ledger' => $recentTransactions,
        ], 'Financial reconciliation data retrieved successfully');
    }

    public function salesAnalytics(Request $request)
    {
        // 1. Peak Shopping Hours (last 30 days)
        $hourlyPeaks = Order::select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // 2. Top Performing Categories
        $topCategories = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(order_items.subtotal) as total_sales'))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_sales', 'desc')
            ->limit(5)
            ->get();

        // 3. Fast Moving Items
        $fastMoving = OrderItem::select('product_name', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_name')
            ->orderBy('total_qty', 'desc')
            ->limit(10)
            ->get();

        return $this->successResponse([
            'hourly_peaks' => $hourlyPeaks,
            'top_categories' => $topCategories,
            'fast_moving_items' => $fastMoving,
        ], 'Sales analytics retrieved successfully');
    }

    public function vendorPerformance(Request $request)
    {
        $performance = Merchant::select(
                'merchants.id',
                'merchants.store_name',
                DB::raw('count(order_items.id) as total_orders'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                'merchants.rating'
            )
            ->leftJoin('order_items', 'merchants.id', '=', 'order_items.merchant_id')
            ->groupBy('merchants.id', 'merchants.store_name', 'merchants.rating')
            ->orderBy('total_revenue', 'desc')
            ->paginate(20);

        return $this->successResponse($performance, 'Vendor performance reports retrieved successfully');
    }

    public function systemSettings(Request $request)
    {
        $pricingRules = DeliveryPricingRule::all();

        return $this->successResponse([
            'pricing_rules' => $pricingRules,
            'platform_commission_rate' => 0.05,
            'active_operational_zones' => [
                ['city' => 'Dar es Salaam', 'radius' => 25],
                ['city' => 'Moshi', 'radius' => 15],
            ]
        ], 'System settings retrieved successfully');
    }

    public function deliveryLogistics(Request $request)
    {
        // 1. Rider Status Overview
        $riderStats = Rider::select('is_online', DB::raw('count(*) as count'))
            ->groupBy('is_online')
            ->get();

        // 2. Active Deliveries (In Progress)
        $activeDeliveries = Order::with(['rider.user', 'customer', 'merchant'])
            ->whereIn('status', ['confirmed', 'processing', 'picked_up', 'out_for_delivery'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // 3. Logistics Performance
        $performance = Rider::select(
                'riders.id',
                'users.name',
                'riders.rating',
                'riders.total_deliveries',
                DB::raw('AVG(actual_duration_minutes) as avg_time')
            )
            ->join('users', 'riders.user_id', '=', 'users.id')
            ->leftJoin('orders', 'riders.id', '=', 'orders.rider_id')
            ->groupBy('riders.id', 'users.name', 'riders.rating', 'riders.total_deliveries')
            ->orderBy('avg_time', 'asc')
            ->limit(10)
            ->get();

        return $this->successResponse([
            'stats' => $riderStats,
            'active_deliveries' => $activeDeliveries,
            'top_performers' => $performance,
        ], 'Delivery logistics data retrieved successfully');
    }

    public function updateDeliveryPricing(Request $request)
    {
        $request->validate([
            'zone_name' => 'required|string',
            'base_fee' => 'required|numeric',
            'per_km_fee' => 'required|numeric',
            'surge_multiplier' => 'sometimes|numeric',
        ]);

        $rule = DeliveryPricingRule::updateOrCreate(
            ['zone_name' => $request->zone_name],
            $request->only(['base_fee', 'per_km_fee', 'surge_multiplier', 'min_basket_value_for_free_delivery'])
        );

        return $this->successResponse($rule, 'Delivery pricing updated successfully');
    }

    public function expansionMetrics(Request $request)
    {
        // 1. Most Demanded Products (from Search Logs where has_results is false)
        $unmetDemand = SearchLog::select('query', DB::raw('count(*) as search_count'))
            ->where('has_results', false)
            ->groupBy('query')
            ->orderBy('search_count', 'desc')
            ->limit(10)
            ->get();

        // 2. Waitlist by Location (Aggregated)
        $waitlistHotspots = Waitlist::select('city', DB::raw('count(*) as count'))
            ->groupBy('city')
            ->orderBy('count', 'desc')
            ->get();

        // 3. Search Hotspots (where users are searching from)
        $searchHotspots = SearchLog::select('latitude', 'longitude', DB::raw('count(*) as count'))
            ->groupBy('latitude', 'longitude')
            ->orderBy('count', 'desc')
            ->limit(20)
            ->get();

        // 4. Overall stats
        $totalWaitlist = Waitlist::count();
        $totalMissedSearches = SearchLog::where('has_results', false)->count();

        return $this->successResponse([
            'unmet_demand' => $unmetDemand,
            'waitlist_hotspots' => $waitlistHotspots,
            'search_hotspots' => $searchHotspots,
            'total_waitlist' => $totalWaitlist,
            'total_missed_searches' => $totalMissedSearches,
        ], 'Expansion metrics retrieved successfully');
    }


    public function orders(Request $request)
    {
        $query = Order::with(['customer', 'rider.user', 'orderItems']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $this->paginateQuery($query, $request, 50, 200);

        return $this->paginatedResponse($orders, 'Orders retrieved successfully');
    }

    public function merchants(Request $request)
    {
        $query = Merchant::with('user');

        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->is_verified);
        }

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        $merchants = $this->paginateQuery($query, $request, 50, 200);

        return $this->paginatedResponse($merchants, 'Merchants retrieved successfully');
    }

    public function riders(Request $request)
    {
        $query = Rider::with('user');

        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->is_verified);
        }

        if ($request->has('is_online')) {
            $query->where('is_online', $request->is_online);
        }

        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        $riders = $this->paginateQuery($query, $request, 50, 200);

        return $this->paginatedResponse($riders, 'Riders retrieved successfully');
    }

    public function storeRider(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6',
            'city' => 'required|string',
            'vehicle_type' => 'required|in:motorcycle,bicycle,car',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        try {
            return DB::transaction(function () use ($request) {
                $user = \App\Models\User::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email ?? ($request->phone . '@patapoa.com'),
                    'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                    'user_type' => 'rider',
                    'is_active' => true,
                    'is_verified' => true,
                    'phone_verified_at' => now(),
                ]);

                $user->wallet()->create([
                    'wallet_type' => 'rider',
                    'balance' => 0,
                    'currency' => 'TZS',
                ]);

                $rider = $user->rider()->create([
                    'vehicle_type' => $request->vehicle_type,
                    'city' => $request->city,
                    'is_online' => false,
                    'is_verified' => true,
                ]);

                return $this->successResponse($rider->load('user'), 'Delivery partner created successfully', 201);
            });
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create rider: ' . $e->getMessage(), 500);
        }
    }

    public function transactions(Request $request)
    {
        $query = Transaction::with(['user', 'order']);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $this->paginateQuery($query, $request, 50, 200);

        return $this->paginatedResponse($transactions, 'Transactions retrieved successfully');
    }

    public function verifyMerchant(Request $request, $id)
    {
        $merchant = Merchant::with('user')->findOrFail($id);
        $merchant->update(['is_verified' => true, 'is_online' => true]);

        // Clear all API caches to ensure new merchant's products show up immediately
        $this->globalCacheFlush();

        // Send notification to merchant
        if ($merchant->user) {
            $this->notifications->sendToUser(
                $merchant->user,
                'Account Verified',
                'Your merchant account has been verified! You can now start selling.',
                ['type' => 'verification_success']
            );
        }

        return $this->successResponse(null, 'Merchant verified successfully');
    }

    public function verifyRider(Request $request, $id)
    {
        $rider = Rider::with('user')->findOrFail($id);
        $rider->update(['is_verified' => true]);

        // Clear API caches
        $this->globalCacheFlush();

        // Send notification to rider
        if ($rider->user) {
            $this->notifications->sendToUser(
                $rider->user,
                'Account Verified',
                'Your rider account has been verified! Go online to receive delivery requests.',
                ['type' => 'verification_success']
            );
        }

        return $this->successResponse(null, 'Rider verified successfully');
    }

    /**
     * 7. Notifications on available products / Marketing
     */
    public function sendMarketingBroadcast(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $data = ['type' => 'marketing'];
        if ($request->product_id) {
            $data['product_id'] = $request->product_id;
        }

        $this->notifications->sendToTopic('all_users', $request->title, $request->body, $data);

        return $this->successResponse(null, 'Broadcast sent successfully');
    }
}
