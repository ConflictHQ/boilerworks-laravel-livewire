<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Livewire\Categories\CategoryForm;
use App\Livewire\Categories\CategoryIndex;
use App\Livewire\Items\ItemForm;
use App\Livewire\Items\ItemIndex;
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

    // Items -- create routes before show to avoid {item} catching "create"
    Route::middleware('permission:items.create')->group(function () {
        Route::get('items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('items', [ItemController::class, 'store'])->name('items.store');
    });
    Route::middleware('permission:items.view')->group(function () {
        Route::get('items', [ItemController::class, 'index'])->name('items.index');
        Route::get('items/{item}', [ItemController::class, 'show'])->name('items.show');
    });
    Route::middleware('permission:items.edit')->group(function () {
        Route::get('items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('items/{item}', [ItemController::class, 'update'])->name('items.update');
    });
    Route::delete('items/{item}', [ItemController::class, 'destroy'])
        ->middleware('permission:items.delete')
        ->name('items.destroy');

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

    // Livewire full-page components -- Items
    Route::middleware('permission:items.view')->group(function () {
        Route::get('lw/items', ItemIndex::class)->name('livewire.items.index');
    });
    Route::middleware('permission:items.create')->group(function () {
        Route::get('lw/items/create', ItemForm::class)->name('livewire.items.create');
    });
    Route::middleware('permission:items.edit')->group(function () {
        Route::get('lw/items/{item}/edit', ItemForm::class)->name('livewire.items.edit');
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
