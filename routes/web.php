<?php

use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\Auth\AdminAuthController;
// Frontend Controllers
use App\Http\Controllers\Admin\CategoryController;
// Admin Controllers
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CourierPartnerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LocationLookupController;
use App\Http\Controllers\Admin\PostalCodeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ShippingClassController;
use App\Http\Controllers\Admin\ShippingMethodController;
use App\Http\Controllers\Admin\ShippingPackageController;
use App\Http\Controllers\Admin\ShippingSettingController;
use App\Http\Controllers\Admin\ShippingZoneController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VariantTypeController;
use App\Http\Controllers\Admin\VariantValueController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Middleware\AdminNoCache;
use Illuminate\Support\Facades\Auth;
// Middleware
use Illuminate\Support\Facades\Route;

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
        Route::get('/products', [ProductController::class, 'index'])
            ->middleware('permission:products.view,admin')
            ->name('products.index');

        Route::get('/products/create', [ProductController::class, 'create'])
            ->middleware('permission:products.create,admin')
            ->name('products.create');

        Route::post('/products', [ProductController::class, 'store'])
            ->middleware('permission:products.create,admin')
            ->name('products.store');

        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
            ->middleware('permission:products.edit,admin')
            ->name('products.edit');

        Route::put('/products/{product}', [ProductController::class, 'update'])
            ->middleware('permission:products.edit,admin')
            ->name('products.update');

        Route::delete('/products/{product}', [ProductController::class, 'destroy'])
            ->middleware('permission:products.delete,admin')
            ->name('products.destroy');

        // Admin Users Management
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

        // variant Types
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

        // variant Values
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

        // Location Management
        // Location lookups (searchable dropdowns)
        Route::get('/locations/lookup/states', [LocationLookupController::class, 'states'])
            ->middleware('permission:cities.view|cities.create|cities.edit|warehouses.create|warehouses.edit|shipping-zones.create|shipping-zones.edit,admin')
            ->name('locations.lookup.states');

        Route::get('/locations/lookup/cities', [LocationLookupController::class, 'cities'])
            ->middleware('permission:postal-codes.view|postal-codes.create|postal-codes.edit|areas.view|areas.create|areas.edit|warehouses.create|warehouses.edit|shipping-zones.create|shipping-zones.edit,admin')
            ->name('locations.lookup.cities');

        Route::get('/locations/lookup/postal-codes', [LocationLookupController::class, 'postalCodes'])
            ->middleware('permission:areas.create|areas.edit|warehouses.create|warehouses.edit,admin')
            ->name('locations.lookup.postal-codes');

        Route::get('/locations/lookup/areas', [LocationLookupController::class, 'areas'])
            ->middleware('permission:warehouses.create|warehouses.edit,admin')
            ->name('locations.lookup.areas');

        // Countries
        Route::get('/countries', [CountryController::class, 'index'])
            ->middleware('permission:countries.view,admin')
            ->name('countries.index');

        Route::get('/countries/create', [CountryController::class, 'create'])
            ->middleware('permission:countries.create,admin')
            ->name('countries.create');

        Route::post('/countries', [CountryController::class, 'store'])
            ->middleware('permission:countries.create,admin')
            ->name('countries.store');

        Route::get('/countries/{country}/edit', [CountryController::class, 'edit'])
            ->middleware('permission:countries.edit,admin')
            ->name('countries.edit');

        Route::put('/countries/{country}', [CountryController::class, 'update'])
            ->middleware('permission:countries.edit,admin')
            ->name('countries.update');

        Route::delete('/countries/{country}', [CountryController::class, 'destroy'])
            ->middleware('permission:countries.delete,admin')
            ->name('countries.destroy');

        // States
        Route::get('/states', [StateController::class, 'index'])
            ->middleware('permission:states.view,admin')
            ->name('states.index');

        Route::get('/states/create', [StateController::class, 'create'])
            ->middleware('permission:states.create,admin')
            ->name('states.create');

        Route::post('/states', [StateController::class, 'store'])
            ->middleware('permission:states.create,admin')
            ->name('states.store');

        Route::get('/states/{state}/edit', [StateController::class, 'edit'])
            ->middleware('permission:states.edit,admin')
            ->name('states.edit');

        Route::put('/states/{state}', [StateController::class, 'update'])
            ->middleware('permission:states.edit,admin')
            ->name('states.update');

        Route::delete('/states/{state}', [StateController::class, 'destroy'])
            ->middleware('permission:states.delete,admin')
            ->name('states.destroy');

        // Cities
        Route::get('/cities', [CityController::class, 'index'])
            ->middleware('permission:cities.view,admin')
            ->name('cities.index');

        Route::get('/cities/create', [CityController::class, 'create'])
            ->middleware('permission:cities.create,admin')
            ->name('cities.create');

        Route::post('/cities', [CityController::class, 'store'])
            ->middleware('permission:cities.create,admin')
            ->name('cities.store');

        Route::get('/cities/{city}/edit', [CityController::class, 'edit'])
            ->middleware('permission:cities.edit,admin')
            ->name('cities.edit');

        Route::put('/cities/{city}', [CityController::class, 'update'])
            ->middleware('permission:cities.edit,admin')
            ->name('cities.update');

        Route::delete('/cities/{city}', [CityController::class, 'destroy'])
            ->middleware('permission:cities.delete,admin')
            ->name('cities.destroy');

        // Postal Codes
        Route::get('/postal-codes', [PostalCodeController::class, 'index'])
            ->middleware('permission:postal-codes.view,admin')
            ->name('postal-codes.index');

        Route::get('/postal-codes/create', [PostalCodeController::class, 'create'])
            ->middleware('permission:postal-codes.create,admin')
            ->name('postal-codes.create');

        Route::post('/postal-codes', [PostalCodeController::class, 'store'])
            ->middleware('permission:postal-codes.create,admin')
            ->name('postal-codes.store');

        Route::get('/postal-codes/{postal_code}/edit', [PostalCodeController::class, 'edit'])
            ->middleware('permission:postal-codes.edit,admin')
            ->name('postal-codes.edit');

        Route::put('/postal-codes/{postal_code}', [PostalCodeController::class, 'update'])
            ->middleware('permission:postal-codes.edit,admin')
            ->name('postal-codes.update');

        Route::delete('/postal-codes/{postal_code}', [PostalCodeController::class, 'destroy'])
            ->middleware('permission:postal-codes.delete,admin')
            ->name('postal-codes.destroy');

        // Areas
        Route::get('/areas', [AreaController::class, 'index'])
            ->middleware('permission:areas.view,admin')
            ->name('areas.index');

        Route::get('/areas/create', [AreaController::class, 'create'])
            ->middleware('permission:areas.create,admin')
            ->name('areas.create');

        Route::post('/areas', [AreaController::class, 'store'])
            ->middleware('permission:areas.create,admin')
            ->name('areas.store');

        Route::get('/areas/{area}/edit', [AreaController::class, 'edit'])
            ->middleware('permission:areas.edit,admin')
            ->name('areas.edit');

        Route::put('/areas/{area}', [AreaController::class, 'update'])
            ->middleware('permission:areas.edit,admin')
            ->name('areas.update');

        Route::delete('/areas/{area}', [AreaController::class, 'destroy'])
            ->middleware('permission:areas.delete,admin')
            ->name('areas.destroy');

        // Shipping Management
        // Warehouses
        Route::get('/shipping/warehouses', [WarehouseController::class, 'index'])
            ->middleware('permission:warehouses.view,admin')
            ->name('warehouses.index');

        Route::get('/shipping/warehouses/create', [WarehouseController::class, 'create'])
            ->middleware('permission:warehouses.create,admin')
            ->name('warehouses.create');

        Route::post('/shipping/warehouses', [WarehouseController::class, 'store'])
            ->middleware('permission:warehouses.create,admin')
            ->name('warehouses.store');

        Route::get('/shipping/warehouses/{warehouse}/edit', [WarehouseController::class, 'edit'])
            ->middleware('permission:warehouses.edit,admin')
            ->name('warehouses.edit');

        Route::put('/shipping/warehouses/{warehouse}', [WarehouseController::class, 'update'])
            ->middleware('permission:warehouses.edit,admin')
            ->name('warehouses.update');

        Route::delete('/shipping/warehouses/{warehouse}', [WarehouseController::class, 'destroy'])
            ->middleware('permission:warehouses.delete,admin')
            ->name('warehouses.destroy');

        // Courier Partners
        Route::get('/shipping/courier-partners', [CourierPartnerController::class, 'index'])
            ->middleware('permission:courier-partners.view,admin')
            ->name('courier-partners.index');

        Route::get('/shipping/courier-partners/create', [CourierPartnerController::class, 'create'])
            ->middleware('permission:courier-partners.create,admin')
            ->name('courier-partners.create');

        Route::post('/shipping/courier-partners', [CourierPartnerController::class, 'store'])
            ->middleware('permission:courier-partners.create,admin')
            ->name('courier-partners.store');

        Route::get('/shipping/courier-partners/{courier_partner}/edit', [CourierPartnerController::class, 'edit'])
            ->middleware('permission:courier-partners.edit,admin')
            ->name('courier-partners.edit');

        Route::put('/shipping/courier-partners/{courier_partner}', [CourierPartnerController::class, 'update'])
            ->middleware('permission:courier-partners.edit,admin')
            ->name('courier-partners.update');

        Route::delete('/shipping/courier-partners/{courier_partner}', [CourierPartnerController::class, 'destroy'])
            ->middleware('permission:courier-partners.delete,admin')
            ->name('courier-partners.destroy');

        // Shipping Methods
        Route::get('/shipping/shipping-methods', [ShippingMethodController::class, 'index'])
            ->middleware('permission:shipping-methods.view,admin')
            ->name('shipping-methods.index');

        Route::get('/shipping/shipping-methods/create', [ShippingMethodController::class, 'create'])
            ->middleware('permission:shipping-methods.create,admin')
            ->name('shipping-methods.create');

        Route::post('/shipping/shipping-methods', [ShippingMethodController::class, 'store'])
            ->middleware('permission:shipping-methods.create,admin')
            ->name('shipping-methods.store');

        Route::get('/shipping/shipping-methods/{shipping_method}/edit', [ShippingMethodController::class, 'edit'])
            ->middleware('permission:shipping-methods.edit,admin')
            ->name('shipping-methods.edit');

        Route::put('/shipping/shipping-methods/{shipping_method}', [ShippingMethodController::class, 'update'])
            ->middleware('permission:shipping-methods.edit,admin')
            ->name('shipping-methods.update');

        Route::delete('/shipping/shipping-methods/{shipping_method}', [ShippingMethodController::class, 'destroy'])
            ->middleware('permission:shipping-methods.delete,admin')
            ->name('shipping-methods.destroy');

        // Shipping Zones
        Route::get('/shipping/shipping-zones', [ShippingZoneController::class, 'index'])
            ->middleware('permission:shipping-zones.view,admin')
            ->name('shipping-zones.index');

        Route::get('/shipping/shipping-zones/create', [ShippingZoneController::class, 'create'])
            ->middleware('permission:shipping-zones.create,admin')
            ->name('shipping-zones.create');

        Route::post('/shipping/shipping-zones', [ShippingZoneController::class, 'store'])
            ->middleware('permission:shipping-zones.create,admin')
            ->name('shipping-zones.store');

        Route::get('/shipping/shipping-zones/{shipping_zone}/edit', [ShippingZoneController::class, 'edit'])
            ->middleware('permission:shipping-zones.edit,admin')
            ->name('shipping-zones.edit');

        Route::put('/shipping/shipping-zones/{shipping_zone}', [ShippingZoneController::class, 'update'])
            ->middleware('permission:shipping-zones.edit,admin')
            ->name('shipping-zones.update');

        Route::delete('/shipping/shipping-zones/{shipping_zone}', [ShippingZoneController::class, 'destroy'])
            ->middleware('permission:shipping-zones.delete,admin')
            ->name('shipping-zones.destroy');

        // Shipping Packages
        Route::get('/shipping/shipping-packages', [ShippingPackageController::class, 'index'])
            ->middleware('permission:shipping-packages.view,admin')
            ->name('shipping-packages.index');

        Route::get('/shipping/shipping-packages/create', [ShippingPackageController::class, 'create'])
            ->middleware('permission:shipping-packages.create,admin')
            ->name('shipping-packages.create');

        Route::post('/shipping/shipping-packages', [ShippingPackageController::class, 'store'])
            ->middleware('permission:shipping-packages.create,admin')
            ->name('shipping-packages.store');

        Route::get('/shipping/shipping-packages/{shipping_package}/edit', [ShippingPackageController::class, 'edit'])
            ->middleware('permission:shipping-packages.edit,admin')
            ->name('shipping-packages.edit');

        Route::put('/shipping/shipping-packages/{shipping_package}', [ShippingPackageController::class, 'update'])
            ->middleware('permission:shipping-packages.edit,admin')
            ->name('shipping-packages.update');

        Route::delete('/shipping/shipping-packages/{shipping_package}', [ShippingPackageController::class, 'destroy'])
            ->middleware('permission:shipping-packages.delete,admin')
            ->name('shipping-packages.destroy');

        // Shipping Classes
        Route::get('/shipping/shipping-classes', [ShippingClassController::class, 'index'])
            ->middleware('permission:shipping-classes.view,admin')
            ->name('shipping-classes.index');

        Route::get('/shipping/shipping-classes/create', [ShippingClassController::class, 'create'])
            ->middleware('permission:shipping-classes.create,admin')
            ->name('shipping-classes.create');

        Route::post('/shipping/shipping-classes', [ShippingClassController::class, 'store'])
            ->middleware('permission:shipping-classes.create,admin')
            ->name('shipping-classes.store');

        Route::get('/shipping/shipping-classes/{shipping_class}/edit', [ShippingClassController::class, 'edit'])
            ->middleware('permission:shipping-classes.edit,admin')
            ->name('shipping-classes.edit');

        Route::put('/shipping/shipping-classes/{shipping_class}', [ShippingClassController::class, 'update'])
            ->middleware('permission:shipping-classes.edit,admin')
            ->name('shipping-classes.update');

        Route::delete('/shipping/shipping-classes/{shipping_class}', [ShippingClassController::class, 'destroy'])
            ->middleware('permission:shipping-classes.delete,admin')
            ->name('shipping-classes.destroy');

        // Shipping Settings
        Route::get('/shipping/settings', [ShippingSettingController::class, 'edit'])
            ->middleware('permission:shipping-settings.view,admin')
            ->name('shipping-settings.edit');

        Route::put('/shipping/settings', [ShippingSettingController::class, 'update'])
            ->middleware('permission:shipping-settings.edit,admin')
            ->name('shipping-settings.update');

        // Logout
        Route::post('/logout', [AdminAuthController::class, 'logout'])
            ->name('logout');
    });

});
