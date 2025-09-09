<?php

use Illuminate\Http\Request;
use App\Livewire\CustomLogin;

// use App\Livewire\Staff\Billing;
// use App\Livewire\Admin\BillingPage;
// use App\Livewire\Admin\ManageAdmin;
// use App\Livewire\Admin\ManageStaff;
use App\Livewire\Staff\DuePayments;
// use App\Livewire\Admin\SupplierList;
use App\Livewire\Admin\ViewPayments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\ManageCustomer;
use App\Livewire\Staff\StaffDashboard;
use App\Livewire\Admin\StaffDueDetails;
// use App\Livewire\Admin\PaymentApprovals;
use App\Livewire\Admin\StaffSaleDetails;
use App\Livewire\Admin\StaffStockDetails;
use App\Livewire\Staff\StaffStockOverview;
use App\Livewire\Admin\CustomerSaleDetails;
use App\Livewire\Staff\CustomerSaleManagement;
use App\Livewire\Admin\StoreBilling;
use App\Livewire\Admin\DuePayments as AdminDuePayments;
use App\Http\Controllers\StaffSaleExportController;
use App\Http\Controllers\WatchesExportController;
use App\Livewire\Admin\Category;
use App\Livewire\Admin\DueCheques;
use App\Livewire\Admin\Products;
use App\Livewire\Admin\DueChequesReturn;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', CustomLogin::class)->name('welcome')->middleware('guest');

// Custom logout route
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Routes that require authentication
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    // !! Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/manage-customer', ManageCustomer::class)->name('manage-customer');
        Route::get('/staff-stock-details', StaffStockDetails::class)->name('staff-stock-details');
        Route::get('/staff-sale-details', StaffSaleDetails::class)->name('staff-sale-details');
        Route::get('/staff-due-details', StaffDueDetails::class)->name('staff-due-details');
        Route::get('/customer-sale-details', CustomerSaleDetails::class)->name('customer-sale-details');
        Route::get('/view-payments', ViewPayments::class)->name('view-payments');
        Route::get('/admin/staff/{staffId}/reentry', \App\Livewire\Admin\StockReentry::class)->name('staff.reentry');
        // Route::get('/store-billing', [StoreBilling::class, 'index'])->name('store-billing');
        Route::get('/store-billing', StoreBilling::class)->name('store-billing');
        Route::get('/due-payments', AdminDuePayments::class)->name('due-payments');
        Route::get('/categories', Category::class)->name('categories');
        Route::get('/products', Products::class)->name('products');
        Route::get('/due-cheques', DueCheques::class)->name('due-cheques');
        Route::get('/due-cheques-return', DueChequesReturn::class)->name('due-cheques-return');
        
        Route::get('/product-stocks', \App\Livewire\Admin\ProductStocks::class)->name('product-stocks');

    });

   
    //!! Staff routes
    Route::middleware('role:staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', StaffDashboard::class)->name('dashboard');
        Route::get('/customer-sale-management', CustomerSaleManagement::class)->name('customer-sale-management');
        Route::get('/staff-stock-overview', StaffStockOverview::class)->name('staff-stock-overview');
        Route::get('/due-payments', DuePayments::class)->name('due-payments');

    });


    // !! Export routes (accessible to authenticated users)

    Route::get('/watches/export', [WatchesExportController::class, 'export'])->name('watches.export')->middleware(['auth']);
    Route::get('/staff-sales/export', [StaffSaleExportController::class, 'export'])
    ->name('staff-sales.export')->middleware(['auth']);
    // Receipt download (accessible to authenticated users)
    Route::get('/receipts/{id}/download', [App\Http\Controllers\ReceiptController::class, 'download'])
        ->name('receipts.download')
        ->middleware(['auth']);

    // Export staff stock details
    Route::get('/export/staff-stock', function() {
        return app(StaffStockDetails::class)->exportToCSV();
    })->name('export.staff-stock');

});
