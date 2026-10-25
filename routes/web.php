<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Frontend Controllers
use App\Http\Controllers\Frontend\HomeController;

// Admin Controllers
use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\VariantTypeController;
use App\Http\Controllers\Admin\VariantValueController;
use App\Http\Controllers\Admin\ProductController;

// Middleware
use App\Http\Middleware\AdminNoCache;

// Frontend Routes
// Route::get('/', [HomeController::class, 'index'])->name('home');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {

    // Admin root URL: /admin
    Route::get('/', function () {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    })->name('home');

    // Guest Routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])
            ->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('login.submit');
    });

    // Authenticated Admin Routes
    Route::middleware([
        'auth:admin', 
        AdminNoCache::class,
    ])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            // ->middleware('permission:dashboard.view,admin')
            ->name('dashboard');

        // Roles & Permissions Management
        Route::get('/roles', [RoleController::class, 'index'])
            // ->middleware('permission:roles.view,admin')
            ->name('roles.index');

        Route::get('/roles/create', [RoleController::class, 'create'])
            ->middleware('permission:roles.create,admin')
            ->name('roles.create');

        Route::post('/roles', [RoleController::class, 'store'])
            ->middleware('permission:roles.create,admin')
            ->name('roles.store');

        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
            ->middleware('permission:roles.edit,admin')
            ->name('roles.edit');

        Route::delete('/roles/{role}/destroy', [RoleController::class, 'destroy'])
            ->middleware('permission:roles.delete,admin')
            ->name('roles.destroy');

        Route::put('/roles/{role}', [RoleController::class, 'update'])
            ->middleware('permission:roles.edit,admin')
            ->name('roles.update');

        // Products
        // Route::get('/products', [ProductController::class, 'index'])
        //     ->middleware('permission:products.view,admin')
        //     ->name('products.index');

        Route::get('/products/create', [ProductController::class, 'create'])
            ->middleware('permission:products.create,admin')
            ->name('products.create');

        //Admin Users Management
        Route::get('/users', [UserController::class, 'index'])
            ->middleware('permission:users.view,admin')
            ->name('users.index');

        Route::get('/users/create', [UserController::class, 'create'])
            ->middleware('permission:users.create,admin')
            ->name('users.create');

        Route::post('/users', [UserController::class, 'store'])
            ->middleware('permission:users.create,admin')
            ->name('users.store');

        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->middleware('permission:users.edit,admin')
            ->name('users.edit');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->middleware('permission:users.edit,admin')
            ->name('users.update');

        Route::delete('/users/{user}/destroy', [UserController::class, 'destroy'])
            ->middleware('permission:users.delete,admin')
            ->name('users.destroy');

        // Category Management
        Route::get('/categories', [CategoryController::class, 'index'])
            ->middleware('permission:categories.view,admin')
            ->name('categories.index');

        Route::get('/categories/create', [CategoryController::class, 'create'])
            ->middleware('permission:categories.create,admin')
            ->name('categories.create');

        Route::post('/categories', [CategoryController::class, 'store'])
            ->middleware('permission:categories.create,admin')
            ->name('categories.store');

        Route::get('/categories/{category}', [CategoryController::class, 'show'])
            ->middleware('permission:categories.view,admin')
            ->name('categories.show');

        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])
            ->middleware('permission:categories.edit,admin')
            ->name('categories.edit');

        Route::put('/categories/{category}', [CategoryController::class, 'update'])
            ->middleware('permission:categories.edit,admin')
            ->name('categories.update');

        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
            ->middleware('permission:categories.delete,admin')
            ->name('categories.destroy');

        //variant Types
        Route::get('/variant-type', [VariantTypeController::class, 'index'])
            // ->middleware('permission:categories.view,admin')
            ->name('variant-type.index');

        Route::get('/variant-type/create', [VariantTypeController::class, 'create'])
            // ->middleware('permission:categories.create,admin')
            ->name('variant-type.create');

        Route::post('/variant-type', [VariantTypeController::class, 'store'])
            // ->middleware('permission:categories.create,admin')
            ->name('variant-type.store');

        Route::get('/variant-type/{variantType}', [VariantTypeController::class, 'show'])
            // ->middleware('permission:categories.view,admin')
            ->name('categvariant-typesories.show');

        Route::get('/variant-type/{variantType}/edit', [VariantTypeController::class, 'edit'])
            // ->middleware('permission:categories.edit,admin')
            ->name('variant-type.edit');

        Route::put('/variant-type/{variantType}', [VariantTypeController::class, 'update'])
            // ->middleware('permission:categories.edit,admin')
            ->name('variant-type.update');

        Route::delete('/variant-type/{variantType}', [VariantTypeController::class, 'destroy'])
            // ->middleware('permission:categories.delete,admin')
            ->name('variant-type.destroy');

        //variant Values
        Route::get('/variant-values', [VariantValueController::class, 'index'])
            // ->middleware('permission:categories.view,admin')
            ->name('variant-values.index');

        Route::get('/variant-values/create', [VariantValueController::class, 'create'])
            // ->middleware('permission:categories.create,admin')
            ->name('variant-values.create');

        Route::post('/variant-values', [VariantValueController::class, 'store'])
            // ->middleware('permission:categories.create,admin')
            ->name('variant-values.store');

        Route::get('/variant-values/{variant_value}/edit', [VariantValueController::class, 'edit'])
            // ->middleware('permission:categories.edit,admin')
            ->name('variant-values.edit');

        Route::put('/variant-values/{variant_value}', [VariantValueController::class, 'update'])
            // ->middleware('permission:categories.edit,admin')
            ->name('variant-values.update');

        Route::delete('/variant-values/{variant_value}', [VariantValueController::class, 'destroy'])
            // ->middleware('permission:categories.delete,admin')
            ->name('variant-values.destroy');

        // Logout
        Route::post('/logout', [AdminAuthController::class, 'logout'])
            ->name('logout');
    });

});