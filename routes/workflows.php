<?php

use App\Http\Controllers\WorkflowDefinitionController;
use App\Http\Controllers\WorkflowInstanceController;
use App\Livewire\Workflows\WorkflowDefinitionIndex;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // Workflow definitions -- create routes before show
    Route::middleware('permission:workflows.create')->group(function () {
        Route::get('workflows/create', [WorkflowDefinitionController::class, 'create'])->name('workflows.create');
        Route::post('workflows', [WorkflowDefinitionController::class, 'store'])->name('workflows.store');
    });
    Route::middleware('permission:workflows.view')->group(function () {
        Route::get('workflows', [WorkflowDefinitionController::class, 'index'])->name('workflows.index');
        Route::get('workflows/{workflow}', [WorkflowDefinitionController::class, 'show'])->name('workflows.show');
    });
    Route::middleware('permission:workflows.edit')->group(function () {
        Route::get('workflows/{workflow}/edit', [WorkflowDefinitionController::class, 'edit'])->name('workflows.edit');
        Route::put('workflows/{workflow}', [WorkflowDefinitionController::class, 'update'])->name('workflows.update');
    });
    Route::delete('workflows/{workflow}', [WorkflowDefinitionController::class, 'destroy'])
        ->middleware('permission:workflows.delete')
        ->name('workflows.destroy');

    // Instances
    Route::get('workflows/{workflow}/instances', [WorkflowInstanceController::class, 'index'])
        ->middleware('permission:workflows.view')
        ->name('workflows.instances.index');
    Route::post('workflows/{workflow}/instances', [WorkflowInstanceController::class, 'store'])
        ->middleware('permission:workflows.create')
        ->name('workflows.instances.store');
    Route::post('workflows/{workflow}/instances/{instance}/transition', [WorkflowInstanceController::class, 'transition'])
        ->middleware('permission:workflows.transition')
        ->name('workflows.instances.transition');

    // Livewire full-page component
    Route::get('lw/workflows', WorkflowDefinitionIndex::class)
        ->middleware('permission:workflows.view')
        ->name('livewire.workflows.index');
});
