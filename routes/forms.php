<?php

use App\Http\Controllers\FormDefinitionController;
use App\Http\Controllers\FormSubmissionController;
use App\Livewire\Forms\FormDefinitionIndex;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // Form definitions -- create routes before show
    Route::middleware('permission:forms.create')->group(function () {
        Route::get('forms/create', [FormDefinitionController::class, 'create'])->name('forms.create');
        Route::post('forms', [FormDefinitionController::class, 'store'])->name('forms.store');
    });
    Route::middleware('permission:forms.view')->group(function () {
        Route::get('forms', [FormDefinitionController::class, 'index'])->name('forms.index');
        Route::get('forms/{form}', [FormDefinitionController::class, 'show'])->name('forms.show');
    });
    Route::middleware('permission:forms.edit')->group(function () {
        Route::get('forms/{form}/edit', [FormDefinitionController::class, 'edit'])->name('forms.edit');
        Route::put('forms/{form}', [FormDefinitionController::class, 'update'])->name('forms.update');
    });
    Route::delete('forms/{form}', [FormDefinitionController::class, 'destroy'])
        ->middleware('permission:forms.delete')
        ->name('forms.destroy');

    // Submissions
    Route::get('forms/{form}/submissions', [FormSubmissionController::class, 'index'])
        ->middleware('permission:forms.view')
        ->name('forms.submissions.index');
    Route::middleware('permission:forms.submit')->group(function () {
        Route::get('forms/{form}/submit', [FormSubmissionController::class, 'create'])->name('forms.submissions.create');
        Route::post('forms/{form}/submit', [FormSubmissionController::class, 'store'])->name('forms.submissions.store');
    });

    // Livewire full-page component
    Route::get('lw/forms', FormDefinitionIndex::class)
        ->middleware('permission:forms.view')
        ->name('livewire.forms.index');
});
