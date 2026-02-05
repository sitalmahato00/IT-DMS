<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

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
        
        // Share notification data across all views via view composer
        \Illuminate\Support\Facades\View::composer('admin.components.header', function($view) {
            $unreadNoticeCount = 0;
            $recentNotices = [];
            
            if (Schema::hasTable('notices')) {
                // Get unread notices count (published notices)
                $unreadNoticeCount = DB::table('notices')
                    ->where('status', 'published')
                    ->count();
                
                // Get recent notices for header dropdown (max 5)
                $recentNotices = DB::table('notices')
                    ->where('status', 'published')
                    ->orderBy('published_at', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function($notice) {
                        return [
                            'id' => $notice->id ?? null,
                            'title' => $notice->title ?? 'Notice',
                            'message' => $notice->message ?? '',
                            'time' => isset($notice->published_at) ? Carbon::parse($notice->published_at)->diffForHumans() : 'Recently',
                            'is_important' => $notice->is_important ?? false,
                        ];
                    })
                    ->toArray();
            }
            
            $view->with('unreadNoticeCount', $unreadNoticeCount);
            $view->with('recentNotices', $recentNotices);
        });
    }
}
