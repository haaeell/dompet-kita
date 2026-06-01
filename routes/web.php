<?php

use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\BillReminderController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\FeatureAnnouncementController;
use App\Http\Controllers\Admin\FeatureAnnouncementController as AdminFeatureAnnouncementController;

Route::view('/privacy', 'legal.privacy')->name('privacy');
Route::view('/terms', 'legal.terms')->name('terms');

Route::get('/', function (\Illuminate\Http\Request $request) {
    if (auth()->check()) {
        return app(DashboardController::class)->index($request);
    }

    return view('welcome');
})->name('dashboard');

// ─── Auth Routes ─────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Protected Routes ─────────────────────────────────────
Route::middleware(['auth'])->group(function () {

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

    Route::prefix('budgets')->name('budgets.')->group(function () {
        Route::get('/', [BudgetController::class, 'index'])->name('index');
        Route::post('/', [BudgetController::class, 'update'])->name('update');
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
        Route::post('/{target}/spend', [TargetController::class, 'spend'])->name('spend');
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
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');

    Route::prefix('reminders')->name('reminders.')->group(function () {
        Route::get('/', [BillReminderController::class, 'index'])->name('index');
        Route::post('/', [BillReminderController::class, 'store'])->name('store');
        Route::put('/{billReminder}/paid', [BillReminderController::class, 'markPaid'])->name('paid');
        Route::delete('/{billReminder}', [BillReminderController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('assets')->name('assets.')->group(function () {
        Route::get('/', [AssetController::class, 'index'])->name('index');
        Route::post('/', [AssetController::class, 'store'])->name('store');
        Route::put('/{asset}', [AssetController::class, 'update'])->name('update');
        Route::delete('/{asset}', [AssetController::class, 'destroy'])->name('destroy');
    });

    // Lokasi Pasangan
    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/', [LocationController::class, 'index'])->name('index');
        Route::post('/', [LocationController::class, 'update'])->name('update');
        Route::delete('/', [LocationController::class, 'destroy'])->name('destroy');
    });

    // Chat Pasangan
    Route::prefix('chats')->name('chats.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/messages', [ChatController::class, 'messages'])->name('messages');
        Route::get('/attachments/{chatMessage}', [ChatController::class, 'attachment'])->name('attachment');
        Route::post('/messages', [ChatController::class, 'store'])->name('store');
        Route::put('/messages/{chatMessage}', [ChatController::class, 'update'])->name('update');
        Route::delete('/messages/{chatMessage}', [ChatController::class, 'destroy'])->name('destroy');
    });

    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');
    Route::put('/feature-announcements/{featureAnnouncement}/read', [FeatureAnnouncementController::class, 'dismiss'])
        ->name('feature-announcements.read');

    Route::middleware(['auth', 'role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/', [AdminDashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('/settings', [SettingController::class, 'index'])
                ->name('settings.index');

            Route::post('/settings/maintenance', [SettingController::class, 'toggleMaintenance'])
                ->name('settings.maintenance');

            Route::get('/feature-announcements', [AdminFeatureAnnouncementController::class, 'index'])
                ->name('feature-announcements.index');
            Route::post('/feature-announcements', [AdminFeatureAnnouncementController::class, 'store'])
                ->name('feature-announcements.store');
            Route::put('/feature-announcements/{featureAnnouncement}', [AdminFeatureAnnouncementController::class, 'update'])
                ->name('feature-announcements.update');
            Route::delete('/feature-announcements/{featureAnnouncement}', [AdminFeatureAnnouncementController::class, 'destroy'])
                ->name('feature-announcements.destroy');
        });
});
