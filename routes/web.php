<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Livewire\Categories\CategoryForm;
use App\Livewire\Categories\CategoryIndex;
use App\Livewire\Products\ProductForm;
use App\Livewire\Products\ProductIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/status', function () {
    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'laravel' => app()->version(),
        'php' => PHP_VERSION,
        'environment' => app()->environment(),
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('status');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Products -- create routes before show to avoid {product} catching "create"
    Route::middleware('permission:products.create')->group(function () {
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
    });
    Route::middleware('permission:products.view')->group(function () {
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
    });
    Route::middleware('permission:products.edit')->group(function () {
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    });
    Route::delete('products/{product}', [ProductController::class, 'destroy'])
        ->middleware('permission:products.delete')
        ->name('products.destroy');

    // Categories -- create routes before show
    Route::middleware('permission:categories.create')->group(function () {
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    });
    Route::middleware('permission:categories.view')->group(function () {
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    });
    Route::middleware('permission:categories.edit')->group(function () {
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    });
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])
        ->middleware('permission:categories.delete')
        ->name('categories.destroy');

    // Livewire full-page components -- Products
    Route::middleware('permission:products.view')->group(function () {
        Route::get('lw/products', ProductIndex::class)->name('livewire.products.index');
    });
    Route::middleware('permission:products.create')->group(function () {
        Route::get('lw/products/create', ProductForm::class)->name('livewire.products.create');
    });
    Route::middleware('permission:products.edit')->group(function () {
        Route::get('lw/products/{product}/edit', ProductForm::class)->name('livewire.products.edit');
    });

    // Livewire full-page components -- Categories
    Route::middleware('permission:categories.view')->group(function () {
        Route::get('lw/categories', CategoryIndex::class)->name('livewire.categories.index');
    });
    Route::middleware('permission:categories.create')->group(function () {
        Route::get('lw/categories/create', CategoryForm::class)->name('livewire.categories.create');
    });
    Route::middleware('permission:categories.edit')->group(function () {
        Route::get('lw/categories/{category}/edit', CategoryForm::class)->name('livewire.categories.edit');
    });
});
