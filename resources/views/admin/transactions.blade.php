@extends('layouts.admin')

@section('title', 'Transaction Ledger')

@section('content')
<!-- Header -->
<div class="flex justify-between items-end">
    <div>
        <h2 class="text-3xl font-black text-on-surface">Transaction Ledger</h2>
        <p class="text-on-surface-variant font-medium">Comprehensive financial record of platform commerce.</p>
    </div>
    <div class="flex gap-2">
        <button class="flex items-center gap-2 px-6 py-2 rounded-xl border border-outline bg-white hover:bg-surface-container transition-colors font-bold">
            <span class="material-symbols-outlined text-[18px]">filter_list</span>
            Filter
        </button>
        <button class="flex items-center gap-2 px-6 py-2 rounded-xl bg-primary text-on-primary hover:opacity-90 transition-opacity font-bold">
            <span class="material-symbols-outlined text-[18px]">download</span>
            Export CSV
        </button>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant flex flex-col justify-between hover:shadow-md transition-shadow">
        <div class="flex justify-between">
            <span class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">Total Sales (Gross)</span>
            <span class="material-symbols-outlined text-primary">payments</span>
        </div>
        <div>
            <p class="text-2xl font-black text-on-surface">TZS {{ number_format($transactions->where('type', 'payment')->where('status', 'completed')->sum('amount')) }}</p>
        </div>
    </div>
    <div class="bg-primary-container/10 p-6 rounded-xl shadow-sm border border-primary-container flex flex-col justify-between hover:shadow-md transition-shadow">
        <div class="flex justify-between">
            <span class="text-xs uppercase tracking-widest text-on-primary-container font-bold">Platform Revenue</span>
            <span class="material-symbols-outlined text-primary">account_balance_wallet</span>
        </div>
        <div>
            @php $platformRevenue = $transactions->where('type', 'payment')->where('status', 'completed')->sum('amount') * 0.05 @endphp
            <p class="text-2xl font-black text-primary">TZS {{ number_format($platformRevenue) }}</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant flex flex-col justify-between hover:shadow-md transition-shadow">
        <div class="flex justify-between">
            <span class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">Pending Payouts</span>
            <span class="material-symbols-outlined text-secondary">hourglass_empty</span>
        </div>
        <div>
            <p class="text-2xl font-black text-on-surface">TZS {{ number_format($transactions->where('type', 'payout')->where('status', 'pending')->sum('amount')) }}</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant flex flex-col justify-between hover:shadow-md transition-shadow">
        <div class="flex justify-between">
            <span class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">Settlements</span>
            <span class="material-symbols-outlined text-error">local_shipping</span>
        </div>
        <div>
            <p class="text-2xl font-black text-error">TZS 0</p>
        </div>
    </div>
</div>

<!-- Main Transactions Table -->
<div class="bg-white rounded-xl shadow-lg border border-outline-variant overflow-hidden">
    <div class="p-4 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
        <h3 class="text-lg font-black">Transaction Logs</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface-container-highest border-b border-outline-variant">
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Transaction ID</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Type</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-widest text-on-surface-variant">User</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-widest text-on-surface-variant text-right">Amount</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-widest text-on-surface-variant text-center">Date</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-widest text-on-surface-variant text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @foreach($transactions as $t)
                <tr class="hover:bg-primary/5 transition-colors cursor-pointer">
                    <td class="px-6 py-4 text-sm font-bold text-primary">PAT-T-{{ $t->id }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[18px]">{{ $t->type === 'payment' ? 'shopping_bag' : 'send_money' }}</span>
                            <span class="text-sm font-medium capitalize">{{ $t->type }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $t->user->name ?? 'System' }}</td>
                    <td class="px-6 py-4 text-sm font-black text-right">TZS {{ number_format($t->amount) }}</td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant text-center">{{ $t->created_at->format('M d, H:i') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest {{ $t->status === 'completed' ? 'bg-primary/10 text-primary border border-primary/20' : 'bg-secondary-container text-on-secondary-container border border-outline-variant' }}">
                            {{ $t->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Pagination Footer -->
    <div class="px-6 py-4 bg-surface-container/10 border-t border-outline-variant/20">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
