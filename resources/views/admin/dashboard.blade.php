@extends('layouts.admin')

@section('title', 'Overview')

@section('content')
<!-- KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Revenue Card -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-outline-variant flex flex-col justify-between group hover:border-primary transition-colors">
        <div class="flex justify-between items-start">
            <div class="p-2 bg-primary/10 rounded-lg text-primary">
                <span class="material-symbols-outlined">payments</span>
            </div>
        </div>
        <div class="mt-4">
            <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">Total Platform Revenue</p>
            <h2 class="text-xl font-black mt-1 text-on-surface">TSH {{ number_format($stats['total_revenue']) }}</h2>
        </div>
    </div>
    <!-- Active Deliveries -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-outline-variant flex flex-col justify-between group hover:border-primary transition-colors">
        <div class="flex justify-between items-start">
            <div class="p-2 bg-tertiary/10 rounded-lg text-tertiary">
                <span class="material-symbols-outlined">local_shipping</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                </span>
                <span class="text-xs font-medium text-on-surface-variant">Live</span>
            </div>
        </div>
        <div class="mt-4">
            <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">Active Riders</p>
            <h2 class="text-xl font-black mt-1 text-on-surface">{{ $stats['active_riders'] }} on duty</h2>
        </div>
    </div>
    <!-- New Merchants -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-outline-variant flex flex-col justify-between group hover:border-primary transition-colors">
        <div class="flex justify-between items-start">
            <div class="p-2 bg-secondary/10 rounded-lg text-secondary">
                <span class="material-symbols-outlined">person_add</span>
            </div>
        </div>
        <div class="mt-4">
            <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">New Merchant Signups</p>
            <h2 class="text-xl font-black mt-1 text-on-surface">{{ $stats['new_merchants'] }} today</h2>
        </div>
    </div>
    <!-- Pending Payouts -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-outline-variant flex flex-col justify-between group hover:border-primary transition-colors">
        <div class="flex justify-between items-start">
            <div class="p-2 bg-error-container/30 rounded-lg text-error">
                <span class="material-symbols-outlined">account_balance_wallet</span>
            </div>
        </div>
        <div class="mt-4">
            <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">Total Pending Payouts</p>
            <h2 class="text-xl font-black mt-1 text-on-surface">TSH {{ number_format($stats['pending_payouts']) }}</h2>
        </div>
    </div>
</div>

<!-- Middle Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Platform Growth -->
    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-outline-variant">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-lg font-bold text-on-surface">Platform Activity</h3>
                <p class="text-sm text-on-surface-variant">Real-time engagement tracking</p>
            </div>
        </div>
        <div class="h-64 w-full relative flex items-end gap-2">
            <div class="flex-1 bg-primary/10 hover:bg-primary transition-all h-[30%] rounded-t-lg"></div>
            <div class="flex-1 bg-primary/10 hover:bg-primary transition-all h-[45%] rounded-t-lg"></div>
            <div class="flex-1 bg-primary/10 hover:bg-primary transition-all h-[40%] rounded-t-lg"></div>
            <div class="flex-1 bg-primary/10 hover:bg-primary transition-all h-[60%] rounded-t-lg"></div>
            <div class="flex-1 bg-primary/10 hover:bg-primary transition-all h-[75%] rounded-t-lg"></div>
            <div class="flex-1 bg-primary/20 hover:bg-primary transition-all h-[85%] rounded-t-lg border-t-4 border-primary"></div>
            <div class="flex-1 bg-primary/10 hover:bg-primary transition-all h-[80%] rounded-t-lg"></div>
            <div class="flex-1 bg-primary/10 hover:bg-primary transition-all h-[95%] rounded-t-lg border-t-4 border-primary"></div>
            <div class="flex-1 bg-primary/10 hover:bg-primary transition-all h-[90%] rounded-t-lg"></div>
        </div>
    </div>

    <!-- Top Merchants -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant">
        <h3 class="text-lg font-bold text-on-surface mb-6">Top Performing Merchants</h3>
        <div class="space-y-4">
            @foreach($topMerchants as $merchant)
            <div class="flex items-center justify-between p-2 rounded-lg hover:bg-surface-container-low transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-container-highest flex items-center justify-center overflow-hidden">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($merchant->store_name) }}&background=006d3b&color=fff" class="w-full h-full object-cover"/>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-on-surface">{{ $merchant->store_name }}</h4>
                        <p class="text-xs text-on-surface-variant">{{ $merchant->orders_count }} orders</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-black text-primary">TSH {{ number_format($merchant->revenue / 1000, 1) }}k</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Bottom Section: Recent Activity -->
<div class="bg-white rounded-xl shadow-sm border border-outline-variant overflow-hidden">
    <div class="p-6 border-b border-outline-variant flex justify-between items-center">
        <h3 class="text-lg font-bold text-on-surface">Recent Activity</h3>
        <a href="{{ route('admin.transactions') }}" class="text-primary font-bold text-xs uppercase tracking-widest hover:underline">View All</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface-container-low">
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Event</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-widest text-on-surface-variant">User</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Amount</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @foreach($recentActivity as $activity)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary text-lg">{{ $activity->type === 'payment' ? 'package_2' : 'account_balance_wallet' }}</span>
                            <span class="text-sm font-medium">{{ $activity->description }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $activity->user->name ?? 'System' }}</td>
                    <td class="px-6 py-4 text-sm font-bold">TSH {{ number_format($activity->amount) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-widest {{ $activity->status === 'completed' ? 'bg-primary/10 text-primary' : 'bg-error-container text-error' }}">
                            {{ $activity->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
