<?php

namespace App\Providers;

use App\Models\ContentSection;
use Illuminate\Support\ServiceProvider;
use App\Observers\ContentSectionObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any components services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any components services.
     */
    public function boot(): void
    {
        if (env('APP_ENV') != "local") {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        ContentSection::observe(ContentSectionObserver::class);
    }
}
