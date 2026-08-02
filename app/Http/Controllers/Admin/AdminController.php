<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Rider;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Waitlist;
use App\Models\SearchLog;
use App\Models\DeliveryPricingRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_revenue' => Transaction::where('type', 'payment')->where('status', 'completed')->sum('amount'),
            'active_riders' => Rider::where('is_online', true)->count(),
            'new_merchants' => Merchant::whereDate('created_at', now())->count(),
            'pending_payouts' => Transaction::where('type', 'payout')->where('status', 'pending')->sum('amount'),
            'total_orders' => Order::count(),
            'daily_gmv' => Order::whereDate('created_at', today())->whereIn('payment_status', ['paid', 'completed'])->sum('total'),
            'platform_earnings' => Order::where('payment_status', 'paid')->sum('platform_fee'),
            'total_users' => User::count(),
            'total_products' => \App\Models\Product::count(),
        ];

        $recentActivity = Transaction::with(['user', 'order'])
            ->latest()
            ->take(5)
            ->get();

        $topMerchants = Merchant::withCount('orders')
            ->get()
            ->map(function ($merchant) {
                $merchant->revenue = $merchant->orders()->sum('subtotal');
                return $merchant;
            })
            ->sortByDesc('revenue')
            ->take(3);

        // Expansion & System Data
        $unmetDemand = collect();
        $waitlistHotspots = collect();
        $systemData = [
            'customers_count' => User::where('user_type', 'customer')->count(),
            'merchants_count' => User::where('user_type', 'merchant')->count(),
            'riders_count' => User::where('user_type', 'rider')->count(),
            'active_orders_count' => Order::whereIn('status', ['placed', 'confirmed', 'processing', 'picked_up', 'out_for_delivery'])->count(),
        ];

        try {
            $unmetDemand = SearchLog::select('query', DB::raw('count(*) as search_count'))
                ->where('has_results', false)
                ->groupBy('query')
                ->orderBy('search_count', 'desc')
                ->limit(5)
                ->get();

            $waitlistHotspots = Waitlist::select('city', DB::raw('count(*) as count'))
                ->groupBy('city')
                ->orderBy('count', 'desc')
                ->get();
        } catch (\Exception $e) {
            \Log::warning('Admin Dashboard: Expansion tables missing.');
        }

        return view('admin.dashboard', compact('stats', 'recentActivity', 'topMerchants', 'unmetDemand', 'waitlistHotspots', 'systemData'));
    }

    public function orders()
    {
        $orders = Order::with(['customer'])
            ->latest()
            ->paginate(20);

        return view('admin.orders', compact('orders'));
    }

    public function merchants()
    {
        $merchants = Merchant::with(['user'])
            ->withCount('orders')
            ->latest()
            ->paginate(15);

        return view('admin.merchants', compact('merchants'));
    }

    public function verifyMerchant($id)
    {
        $merchant = Merchant::findOrFail($id);
        $merchant->update(['is_verified' => true, 'is_online' => true]);

        return back()->with('success', 'Merchant verified successfully');
    }

    public function deliveries()
    {
        $riders = Rider::with(['user'])
            ->withCount('orders')
            ->latest()
            ->paginate(15);

        $activeDeliveries = Order::with(['rider.user', 'customer', 'merchant'])
            ->whereIn('status', ['confirmed', 'processing', 'picked_up', 'out_for_delivery'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.deliveries', compact('riders', 'activeDeliveries'));
    }

    public function storeRider(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6',
            'city' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->phone . '@patapoa.com',
                    'password' => Hash::make($request->password),
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

                $user->rider()->create([
                    'vehicle_type' => $request->vehicle_type ?? 'motorcycle',
                    'city' => $request->city,
                    'is_online' => false,
                    'is_verified' => true,
                ]);
            });
            return back()->with('success', 'Delivery partner added successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function storeMerchant(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6',
            'city' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->owner_name,
                    'phone' => $request->phone,
                    'email' => $request->phone . '@patapoa.com',
                    'password' => Hash::make($request->password),
                    'user_type' => 'merchant',
                    'is_active' => true,
                    'is_verified' => true,
                    'phone_verified_at' => now(),
                ]);

                $user->wallet()->create([
                    'wallet_type' => 'merchant',
                    'balance' => 0,
                    'currency' => 'TZS',
                ]);

                $user->merchant()->create([
                    'store_name' => $request->store_name,
                    'address' => 'Pending Setup',
                    'city' => $request->city,
                    'is_verified' => true,
                    'is_online' => true,
                ]);
            });
            return back()->with('success', 'Supermarket partner added successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', 'User status updated');
    }

    public function settings()
    {
        try {
            $pricingRules = DeliveryPricingRule::all();
        } catch (\Exception $e) {
            $pricingRules = collect();
            \Log::warning('Admin Settings: Delivery pricing table missing. Please run php artisan migrate.');
        }
        return view('admin.settings', compact('pricingRules'));
    }

    public function updatePricing(Request $request)
    {
        $request->validate([
            'zone_name' => 'required|string',
            'base_fee' => 'required|numeric',
            'per_km_fee' => 'required|numeric',
        ]);

        DeliveryPricingRule::updateOrCreate(
            ['zone_name' => $request->zone_name],
            $request->only(['base_fee', 'per_km_fee', 'surge_multiplier', 'min_basket_value_for_free_delivery'])
        );

        return back()->with('success', 'Pricing updated');
    }

    public function transactions()
    {
        $transactions = Transaction::with(['user', 'order'])
            ->latest()
            ->paginate(25);

        return view('admin.transactions', compact('transactions'));
    }

    /**
     * Development Utility: Manually mark an order as paid
     */
    public function markAsPaid(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->payment_status === 'paid') {
            return back()->with('error', 'Order is already marked as paid');
        }

        DB::transaction(function() use ($order) {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            // Create a dummy successful transaction if none exists
            Transaction::updateOrCreate(
                ['order_id' => $order->id, 'type' => 'payment'],
                [
                    'user_id' => $order->customer_id,
                    'status' => 'completed',
                    'amount' => $order->total,
                    'currency' => 'TZS',
                    'payment_method' => $order->payment_method ?? 'mpesa',
                    'description' => 'Manual Confirmation (Dev Utility)',
                    'processed_at' => now(),
                ]
            );
        });

        return back()->with('success', 'Order #' . $order->display_id . ' marked as paid successfully.');
    }
}
