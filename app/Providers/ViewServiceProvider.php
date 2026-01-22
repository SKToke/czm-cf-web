<?php

namespace App\Providers;
use App\Models\Content;
use Illuminate\Support\Facades\View;

use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $footerNews = Content::news()->orderBy('created_at', 'desc')->limit(3)->get();
            $view->with('footerNews', $footerNews);
        });
    }
}
