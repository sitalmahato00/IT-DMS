<?php

namespace App\Providers;

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
        // Apply user-selected locale (if any) from session so the __('...') helper uses correct language.
        try {
            $locale = session('locale', config('app.locale'));
            app()->setLocale($locale);
        } catch (\Exception $e) {
            // session may not be available in some contexts; silently ignore
        }

        // Make supported locales available in views via config('locales.supported')
    }
}
