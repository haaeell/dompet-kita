<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\ReportController;

// ─── Auth Routes ─────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Protected Routes ─────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Transaksi
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::post('/', [TransactionController::class, 'store'])->name('store');
        Route::put('/{transaction}', [TransactionController::class, 'update'])->name('update');
        Route::delete('/{transaction}', [TransactionController::class, 'destroy'])->name('destroy');
    });

    // Kategori
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // Bank / Rekening
    Route::prefix('banks')->name('banks.')->group(function () {
        Route::get('/', [BankController::class, 'index'])->name('index');
        Route::get('/transfer', [BankController::class, 'transfer'])->name('transfer');
        Route::post('/transfer', [BankController::class, 'storeTransfer'])->name('transfer.store');
        Route::get('/{bank}/mutations', [BankController::class, 'mutations'])->name('mutations');
        Route::get('/{bank}/mutations/pdf', [BankController::class, 'mutationsPdf'])->name('mutations.pdf');
        Route::post('/', [BankController::class, 'store'])->name('store');
        Route::put('/{bank}', [BankController::class, 'update'])->name('update');
        Route::delete('/{bank}', [BankController::class, 'destroy'])->name('destroy');
    });

    // Target Tabungan
    Route::prefix('targets')->name('targets.')->group(function () {
        Route::get('/', [TargetController::class, 'index'])->name('index');
        Route::post('/', [TargetController::class, 'store'])->name('store');
        Route::post('/{target}/saving', [TargetController::class, 'addSaving'])->name('saving');
        Route::delete('/{target}', [TargetController::class, 'destroy'])->name('destroy');
    });

    // Hutang & Piutang
    Route::prefix('debts')->name('debts.')->group(function () {
        Route::get('/', [App\Http\Controllers\DebtController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\DebtController::class, 'store'])->name('store');
        Route::put('/{debt}/pay', [App\Http\Controllers\DebtController::class, 'pay'])->name('pay');
        Route::delete('/{debt}', [App\Http\Controllers\DebtController::class, 'destroy'])->name('destroy');
    });

    // Laporan
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');
});
