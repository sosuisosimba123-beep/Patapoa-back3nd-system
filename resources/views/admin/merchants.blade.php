@extends('layouts.admin')

@section('title', 'Merchant Management')

@section('content')
<!-- Header Section -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div class="space-y-1">
        <h2 class="text-2xl font-black text-on-surface">Merchant Management</h2>
        <p class="text-sm text-on-surface-variant">Review, approve, and manage all retail partners across the platform.</p>
    </div>
    <button class="flex items-center justify-center gap-2 bg-primary text-on-primary px-6 py-2 rounded-xl font-bold shadow-md hover:shadow-lg active:scale-95 transition-all">
        <span class="material-symbols-outlined">add</span>
        Add New Merchant
    </button>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant flex flex-col justify-between group hover:border-primary transition-colors">
        <div class="flex justify-between items-start mb-2">
            <div class="p-2 bg-primary/10 rounded-lg text-primary">
                <span class="material-symbols-outlined">store</span>
            </div>
        </div>
        <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">Total Merchants</p>
        <h3 class="text-3xl font-black text-on-surface">{{ $merchants->total() }}</h3>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant flex flex-col justify-between group hover:border-tertiary transition-colors">
        <div class="flex justify-between items-start mb-2">
            <div class="p-2 bg-tertiary/10 rounded-lg text-tertiary">
                <span class="material-symbols-outlined">pending_actions</span>
            </div>
        </div>
        <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">Pending Approval</p>
        <h3 class="text-3xl font-black text-on-surface">{{ \App\Models\Merchant::where('is_verified', false)->count() }}</h3>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant flex flex-col justify-between group hover:border-secondary transition-colors">
        <div class="flex justify-between items-start mb-2">
            <div class="p-2 bg-secondary/10 rounded-lg text-secondary">
                <span class="material-symbols-outlined">trending_up</span>
            </div>
        </div>
        <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">Active Stores</p>
        <h3 class="text-3xl font-black text-on-surface">{{ \App\Models\Merchant::where('is_verified', true)->count() }}</h3>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant flex flex-col justify-between group hover:border-error transition-colors">
        <div class="flex justify-between items-start mb-2">
            <div class="p-2 bg-error-container/20 rounded-lg text-error">
                <span class="material-symbols-outlined">block</span>
            </div>
        </div>
        <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">Suspended Accounts</p>
        <h3 class="text-3xl font-black text-on-surface">0</h3>
    </div>
</div>

<!-- Filters & Table Section -->
<div class="bg-white rounded-xl shadow-sm border border-outline-variant overflow-hidden flex flex-col">
    <!-- Table Content -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container/30 border-b border-outline-variant/10">
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Store Name</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Owner</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Status</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-on-surface-variant text-right">Orders</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-on-surface-variant text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                @foreach($merchants as $merchant)
                <tr class="hover:bg-surface-container-low transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-surface-container rounded-lg flex items-center justify-center overflow-hidden">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($merchant->store_name) }}&background=006d3b&color=fff" class="w-full h-full object-cover"/>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-on-surface">{{ $merchant->store_name }}</p>
                                <p class="text-xs text-on-surface-variant">ID: PAT-M-{{ $merchant->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-on-surface">{{ $merchant->user->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $merchant->is_verified ? 'bg-primary' : 'bg-tertiary' }}"></span>
                            <span class="text-[10px] font-bold uppercase tracking-widest {{ $merchant->is_verified ? 'text-primary' : 'text-tertiary' }}">
                                {{ $merchant->is_verified ? 'ACTIVE' : 'PENDING' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-right font-black text-on-surface">{{ $merchant->orders_count }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <button class="p-1 text-on-surface-variant hover:text-primary hover:bg-primary/10 rounded transition-all">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                            <button class="p-1 text-on-surface-variant hover:text-secondary hover:bg-secondary/10 rounded transition-all">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Pagination Footer -->
    <div class="px-6 py-4 bg-surface-container/10 border-t border-outline-variant/20">
        {{ $merchants->links() }}
    </div>
</div>
@endsection
