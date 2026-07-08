<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Rider;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_revenue' => Transaction::where('type', 'payment')->where('status', 'completed')->sum('amount'),
            'active_riders' => Rider::where('is_online', true)->count(),
            'new_merchants' => Merchant::whereDate('created_at', now())->count(),
            'pending_payouts' => Transaction::where('type', 'payout')->where('status', 'pending')->sum('amount'),
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

        return view('admin.dashboard', compact('stats', 'recentActivity', 'topMerchants'));
    }

    public function merchants()
    {
        $merchants = Merchant::with(['user'])
            ->withCount('orders')
            ->latest()
            ->paginate(10);

        return view('admin.merchants', compact('merchants'));
    }

    public function deliveries()
    {
        $riders = Rider::with(['user'])
            ->withCount('orders')
            ->latest()
            ->paginate(10);

        return view('admin.deliveries', compact('riders'));
    }

    public function transactions()
    {
        $transactions = Transaction::with(['user', 'order'])
            ->latest()
            ->paginate(20);

        return view('admin.transactions', compact('transactions'));
    }
}
