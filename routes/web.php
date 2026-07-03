<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\QrScannerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\RestockOrderController;
use App\Http\Controllers\MobileController;
use App\Http\Controllers\MobileDashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierApprovalController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\OrderQueueController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\SearchController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes (Admin/Staff)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Customer Authentication Routes
Route::get('/customer/login', [CustomerAuthController::class, 'showLoginForm'])->name('customer.login');
Route::post('/customer/login', [CustomerAuthController::class, 'login']);
Route::get('/customer/register', [CustomerAuthController::class, 'showRegisterForm'])->name('customer.register');
Route::post('/customer/register', [CustomerAuthController::class, 'register']);
Route::post('/customer/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

// Customer Dashboard & Order Routes
Route::get('/customer/dashboard', [CustomerAuthController::class, 'dashboard'])->name('customer.dashboard');
Route::post('/customer/place-order', [CustomerAuthController::class, 'placeOrder'])->name('customer.place-order');
Route::get('/customer/orders', [CustomerAuthController::class, 'orderHistory'])->name('customer.orders');
Route::get('/customer/order/{id}', [CustomerAuthController::class, 'orderDetails'])->name('customer.order-details');
Route::get('/customer/qr/{id}', [CustomerController::class, 'generateQr'])->name('customer.qr');

// Protected Routes (Admin/Staff)
Route::middleware(['auth'])->group(function () {

    // Global Search
    Route::get('/search', [SearchController::class, 'search'])->name('search');
    Route::get('/search/results', [SearchController::class, 'index'])->name('search.results');

    // Staff Dashboard
    Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard')->middleware('role:staff');

    // Mobile Pulse Check
    Route::get('/mobile-pulse', [MobileDashboardController::class, 'index'])->name('mobile.pulse');
    Route::get('/mobile-pulse/alerts', [MobileDashboardController::class, 'index'])->name('mobile.pulse.alerts');

    // Main Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // MOBILE ROUTES
    Route::prefix('mobile')->name('mobile.')->group(function () {
        Route::get('/test', [MobileController::class, 'test'])->name('test');
        Route::get('/dashboard', [MobileController::class, 'dashboard'])->name('dashboard');
        Route::get('/low-stock', [MobileController::class, 'lowStock'])->name('low-stock');
        Route::get('/sales-summary', [MobileController::class, 'salesSummary'])->name('sales-summary');
        Route::get('/profile', [MobileController::class, 'profile'])->name('profile');
    });

    // Branches - Admin only
    Route::resource('branches', BranchController::class)->middleware('role:admin');

    // Staff - Admin only
    Route::resource('staff', StaffController::class)->middleware('role:admin');

    // Products - Admin and Manager
    Route::resource('products', ProductController::class)->middleware('role:manager');
    Route::post('/products/delete-multiple', [ProductController::class, 'deleteMultiple'])->name('products.delete-multiple')->middleware('role:admin');

    // Recipes - Create product with recipe
    Route::resource('recipes', RecipeController::class);

    // Barista Queue
    Route::get('/barista/queue', [OrderQueueController::class, 'index'])->name('barista.queue');
    Route::post('/barista/order/{id}/status', [OrderQueueController::class, 'updateStatus'])->name('barista.update-status');

    // Suppliers - Admin only
    Route::resource('suppliers', SupplierController::class)->middleware('role:admin');

    // Warehouse
    Route::get('/warehouse', [WarehouseController::class, 'index'])->name('warehouse.index');
    Route::post('/warehouse', [WarehouseController::class, 'store'])->name('warehouse.store')->middleware('role:admin');
    Route::get('/warehouse/transfers', [WarehouseController::class, 'transfers'])->name('warehouse.transfers');
    Route::post('/warehouse/transfer', [WarehouseController::class, 'createTransfer'])->name('warehouse.transfer.store');
    Route::post('/warehouse/transfer/{id}/approve', [WarehouseController::class, 'approveTransfer'])->name('warehouse.transfer.approve');
    Route::post('/warehouse/transfer/{id}/receive', [WarehouseController::class, 'receiveTransfer'])->name('warehouse.transfer.receive');
    Route::post('/warehouse/update-stock', [WarehouseController::class, 'updateStock'])->name('warehouse.update-stock')->middleware('role:admin');
    Route::post('/warehouse/update-threshold', [WarehouseController::class, 'updateThreshold'])->name('warehouse.update-threshold')->middleware('role:admin');
    Route::post('/warehouse/delete-multiple', [WarehouseController::class, 'deleteMultiple'])->name('warehouse.delete-multiple')->middleware('role:admin');

    // Delivery Routes for Staff
    Route::get('/delivery', [DeliveryController::class, 'index'])->name('delivery.index')->middleware('role:staff');
    Route::post('/delivery/receive/{id}', [DeliveryController::class, 'receive'])->name('delivery.receive')->middleware('role:staff');
    Route::post('/delivery/bulk-receive', [DeliveryController::class, 'bulkReceive'])->name('delivery.bulk-receive')->middleware('role:staff');

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store')->middleware('role:admin');
    Route::delete('/inventory/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy')->middleware('role:admin');
    Route::post('/inventory/delete-multiple', [InventoryController::class, 'deleteMultiple'])->name('inventory.delete-multiple')->middleware('role:admin');
    Route::get('/inventory/low-stock', [InventoryController::class, 'getLowStock'])->name('inventory.low-stock');
    Route::post('/inventory/quick-transfer', [InventoryController::class, 'quickTransfer'])->name('inventory.quick-transfer')->middleware('role:admin');

    // POS
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/process', [PosController::class, 'processSale'])->name('pos.process');
    Route::get('/pos/get-stock', [PosController::class, 'getProductStock'])->name('pos.get-stock');
    Route::get('/pos/receipt/{sale}', [PosController::class, 'receipt'])->name('pos.receipt');

    // Customers (Admin/Staff management)
    Route::resource('customers', CustomerController::class);
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::get('/customers/{customer}/qr', [CustomerController::class, 'generateQr'])->name('customers.qr');

    // QR Scanner
    Route::get('/qr-scanner', [QrScannerController::class, 'index'])->name('qr.scanner');
    Route::post('/qr-scan', [QrScannerController::class, 'scan'])->name('qr.scan');
    Route::get('/qr/customer/{id}', [QrScannerController::class, 'viewCustomer'])->name('qr.customer');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');

    // Sync
    Route::get('/sync', [SyncController::class, 'index'])->name('sync.index');
    Route::post('/sync/now', [SyncController::class, 'syncNow'])->name('sync.now');
    Route::post('/sync/offline', [SyncController::class, 'simulateOffline'])->name('sync.offline');
    Route::post('/sync/online', [SyncController::class, 'simulateOnline'])->name('sync.online');
    Route::get('/sync/status', [SyncController::class, 'checkStatus'])->name('sync.status');

    // Settings
    Route::get('/admin/settings', [SettingsController::class, 'index'])->name('admin.settings')->middleware('role:admin');

    // Database Management
    Route::get('/admin/database', [DatabaseController::class, 'index'])->name('admin.database')->middleware('role:admin');
    Route::post('/admin/import/products', [DatabaseController::class, 'importProducts'])->name('admin.import.products')->middleware('role:admin');
    Route::post('/admin/import/items', [DatabaseController::class, 'importItems'])->name('admin.import.items')->middleware('role:admin');
    Route::post('/admin/import/backup', [DatabaseController::class, 'importBackup'])->name('admin.import.backup')->middleware('role:admin');
    Route::get('/admin/export/backup', [DatabaseController::class, 'exportBackup'])->name('admin.export.backup')->middleware('role:admin');
    Route::get('/admin/template/product', [DatabaseController::class, 'downloadProductTemplate'])->name('admin.template.product')->middleware('role:admin');
    Route::get('/admin/template/item', [DatabaseController::class, 'downloadItemTemplate'])->name('admin.template.item')->middleware('role:admin');
});

// Supplier Approval (Public routes - no auth needed)
Route::get('/supplier/approve/{token}', [SupplierApprovalController::class, 'approve'])->name('supplier.approve');
Route::post('/supplier/approve/{token}', [SupplierApprovalController::class, 'approve']);
Route::get('/supplier/reject/{token}', [SupplierApprovalController::class, 'reject'])->name('supplier.reject');
Route::post('/supplier/reject/{token}', [SupplierApprovalController::class, 'reject']);
Route::get('/supplier/view/{token}', [SupplierApprovalController::class, 'view'])->name('supplier.view');