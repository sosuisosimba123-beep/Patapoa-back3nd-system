@extends('layouts.admin')

@section('title', 'Deliverer Fleet')

@section('content')
<!-- Header Section -->
<div class="flex justify-between items-end">
    <div>
        <h2 class="text-3xl font-black text-on-surface">Deliverer Fleet</h2>
        <p class="text-lg text-on-surface-variant mt-1">Managing {{ $riders->total() }} active partners in the Tanzanian logistics network.</p>
    </div>
    <button class="flex items-center gap-2 bg-primary-container text-on-primary-container px-6 py-2 rounded-xl font-bold hover:brightness-95 transition-all shadow-md active:scale-95">
        <span class="material-symbols-outlined">add_circle</span>
        Onboard New Rider
    </button>
</div>

<!-- KPIs Section -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-surface-container-low p-6 rounded-xl border border-outline-variant shadow-sm transition-transform hover:-translate-y-1">
        <div class="flex justify-between items-start mb-2">
            <span class="material-symbols-outlined text-primary text-3xl">groups</span>
        </div>
        <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">Total Riders</p>
        <p class="text-3xl font-black text-on-surface mt-1">{{ $riders->total() }}</p>
    </div>
    <div class="bg-surface-container-low p-6 rounded-xl border border-outline-variant shadow-sm transition-transform hover:-translate-y-1">
        <div class="flex justify-between items-start mb-2">
            <span class="material-symbols-outlined text-primary-container text-3xl">sensors</span>
            <div class="flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-primary-container animate-pulse"></span>
                <span class="text-primary-container font-bold text-xs">Live</span>
            </div>
        </div>
        <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">Online Now</p>
        <p class="text-3xl font-black text-on-surface mt-1">{{ \App\Models\Rider::where('is_online', true)->count() }}</p>
    </div>
    <div class="bg-surface-container-low p-6 rounded-xl border border-outline-variant shadow-sm transition-transform hover:-translate-y-1">
        <div class="flex justify-between items-start mb-2">
            <span class="material-symbols-outlined text-secondary text-3xl">star</span>
        </div>
        <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">Average Rating</p>
        <p class="text-3xl font-black text-on-surface mt-1">4.8</p>
    </div>
    <div class="bg-surface-container-low p-6 rounded-xl border border-outline-variant shadow-sm transition-transform hover:-translate-y-1">
        <div class="flex justify-between items-start mb-2">
            <span class="material-symbols-outlined text-error text-3xl">payments</span>
        </div>
        <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">Total Debt</p>
        <p class="text-3xl font-black text-error mt-1">TSH 0</p>
    </div>
</div>

<!-- Deliverer Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($riders as $rider)
    <div class="bg-white p-4 rounded-xl flex flex-col gap-4 shadow-sm border border-outline-variant hover:border-primary transition-all group">
        <div class="flex items-start gap-4">
            <div class="relative">
                <img class="w-20 h-20 rounded-xl object-cover border-2 border-surface" src="https://ui-avatars.com/api/?name={{ urlencode($rider->user->name ?? 'Rider') }}&background=006d3b&color=fff"/>
                @if($rider->is_online)
                <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-primary-container border-2 border-white rounded-full"></span>
                @endif
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-black text-on-surface">{{ $rider->user->name ?? 'Unknown Rider' }}</h3>
                <div class="flex items-center gap-1 mt-1">
                    <span class="material-symbols-outlined text-xs text-on-surface-variant">{{ $rider->vehicle_type === 'car' ? 'directions_car' : ($rider->vehicle_type === 'bicycle' ? 'pedal_bike' : 'motorcycle') }}</span>
                    <span class="text-sm text-on-surface-variant capitalize">{{ $rider->vehicle_type }}</span>
                </div>
                <div class="flex items-center gap-1 mt-1 text-primary">
                    <span class="material-symbols-outlined text-sm">star</span>
                    <span class="text-sm font-bold">{{ number_format($rider->rating, 1) }}</span>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest {{ $rider->is_online ? 'bg-primary/10 text-primary' : 'bg-secondary/10 text-secondary' }}">
                {{ $rider->is_online ? 'Available' : 'Offline' }}
            </span>
            <span class="px-3 py-1 rounded-full bg-surface-container-highest text-on-surface-variant text-[10px] font-bold uppercase tracking-widest">
                ID: PAT-R-{{ $rider->id }}
            </span>
        </div>
        <div class="border-t border-outline-variant pt-4 flex justify-between items-center">
            <div>
                <p class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold">Completed Trips</p>
                <p class="text-lg font-black text-on-surface">{{ $rider->orders_count }}</p>
            </div>
            <button class="material-symbols-outlined p-2 hover:bg-surface-container-high rounded-full transition-colors">more_vert</button>
        </div>
    </div>
    @endforeach
</div>

<!-- Footer Pagination -->
<div class="mt-6">
    {{ $riders->links() }}
</div>
@endsection
