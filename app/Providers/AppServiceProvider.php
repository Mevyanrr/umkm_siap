<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GeminiService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GeminiService::class);
    }

    public function boot(): void
    {
        //
    }
}
