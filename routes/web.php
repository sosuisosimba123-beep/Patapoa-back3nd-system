<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/merchants', [AdminController::class, 'merchants'])->name('merchants');
    Route::get('/deliveries', [AdminController::class, 'deliveries'])->name('deliveries');
    Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');
});

Route::post('/logout', function () {
    auth()->logout();
    return redirect('/');
})->name('auth.logout');
