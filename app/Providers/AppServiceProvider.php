<?php

namespace App\Providers;

use App\Contracts\NewsScrapperContract;
use App\Library\NewsScrapper;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NewsScrapperContract::class, NewsScrapper::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
