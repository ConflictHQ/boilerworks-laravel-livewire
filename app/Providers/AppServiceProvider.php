<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('features.forms')) {
            Route::middleware('web')
                ->group(base_path('routes/forms.php'));
        }

        if (config('features.workflows')) {
            if (file_exists(base_path('routes/workflows.php'))) {
                Route::middleware('web')
                    ->group(base_path('routes/workflows.php'));
            }
        }
    }
}
