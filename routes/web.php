<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TrashController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'restrict.user'])->group(function () {
    Route::resource('brands', BrandController::class)
        ->missing(fn () => redirect()->route('brands.index'));
    Route::resource('categories', CategoryController::class)
        ->missing(fn () => redirect()->route('categories.index'));
    Route::resource('users', UserController::class)
        ->missing(fn () => redirect()->route('users.index'));
    Route::post('/users/{user}/send-verification', [UserController::class, 'sendVerification'])->name('users.send-verification');
    Route::get('/trash', [TrashController::class, 'index'])->name('trash.index');
    Route::post('/trash/{type}/{id}/restore', [TrashController::class, 'restore'])->name('trash.restore');
    Route::delete('/trash/{type}/{id}', [TrashController::class, 'forceDelete'])->name('trash.force-delete');
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->withoutMiddleware('restrict.user')
        ->name('dashboard');
    Route::resource('products', ProductController::class)
        ->missing(fn () => redirect()->route('products.index'));
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('transactions', InventoryTransactionController::class)
    ->only(['index', 'create', 'store', 'show', 'destroy']);

    Route::post('/notifications/mark-all-read', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return back();
})->name('notifications.markAllRead');

Route::get('/adjustments', [StockAdjustmentController::class, 'create'])->name('adjustments.create');
Route::post('/adjustments', [StockAdjustmentController::class, 'store'])->name('adjustments.store');
Route::get('/adjustments/{product}/quantity', [StockAdjustmentController::class, 'currentQuantity'])->name('adjustments.quantity');

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/stock-movement', [ReportController::class, 'stockMovement'])->name('reports.stock-movement');
Route::get('/reports/low-stock', [ReportController::class, 'lowStock'])->name('reports.low-stock');
Route::get('/reports/valuation', [ReportController::class, 'valuation'])->name('reports.valuation');

    // Unknown URLs should return to the dashboard of the section they belong to
    // instead of falling through to a 404 (or the main dashboard).
    Route::fallback(function (Request $request) {
        if (! $request->isMethod('get')) {
            abort(404);
        }

        $sectionPaths = [
            'dashboard' => '/dashboard',
            'products' => '/products',
            'brands' => '/brands',
            'categories' => '/categories',
            'users' => '/users',
            'trash' => '/trash',
            'profile' => '/profile',
        ];

        $section = strtolower((string) $request->segment(1));

        // Use an absolute URL so odd paths like /.../random cannot make
        // Laravel prefix the redirect with the requested directory.
        return redirect()->away(
            $request->getSchemeAndHttpHost().($sectionPaths[$section] ?? '/dashboard')
        );
    });
});

require __DIR__.'/auth.php';
