<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Template Acorn memakai Bootstrap, bukan Tailwind (default Laravel).
        Paginator::useBootstrapFive();

        // Pesan validasi berbahasa Indonesia (lihat lang/id/validation.php).
        $this->app->setLocale('id');
    }
}
