<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use App\Models\AuditLog;
use App\Observers\AuditObserver;
use App\Observers\NotificationObserver;
use App\View\Composers\CollegeComposer;

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

        // Register the mail namespace for email templates
        // This enables <x-mail::layout> syntax in Blade templates
        View::addNamespace('mail', resource_path('views/emails'));

        // Register college details view composer for all views
        View::composer('*', CollegeComposer::class);

        // Make supported locales available in views via config('locales.supported')

        // Register audit logging for models and authentication events if table exists.
        try {
            if (Schema::hasTable('audit_logs')) {
                // Listen for login/logout events
                Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
                    try {
                        $user = $event->user ?? Auth::user();
                        AuditLog::create([
                            'timestamp' => now(),
                            'user_id' => $user->id ?? Auth::id(),
                            'action' => 'login',
                            'model_type' => 'user',
                            'model_id' => $user->id ?? null,
                            'old_values' => null,
                            'new_values' => [
                                'ip' => request()?->ip(),
                                'user_agent' => request()?->userAgent(),
                            ],
                            'ip_address' => request()?->ip(),
                            'user_agent' => request()?->userAgent(),
                        ]);
                    } catch (\Exception $e) {
                        // ignore logging failure
                    }
                });

                Event::listen(\Illuminate\Auth\Events\Logout::class, function ($event) {
                    try {
                        $user = $event->user ?? Auth::user();
                        AuditLog::create([
                            'timestamp' => now(),
                            'user_id' => $user->id ?? Auth::id(),
                            'action' => 'logout',
                            'model_type' => 'user',
                            'model_id' => $user->id ?? null,
                            'old_values' => null,
                            'new_values' => null,
                            'ip_address' => request()?->ip(),
                            'user_agent' => request()?->userAgent(),
                        ]);
                    } catch (\Exception $e) {
                        // ignore logging failure
                    }
                });

                // Listen for failed login attempts
                Event::listen(\Illuminate\Auth\Events\Failed::class, function ($event) {
                    try {
                        $user = $event->user ?? null;
                        AuditLog::create([
                            'timestamp' => now(),
                            'user_id' => $user->id ?? null,
                            'action' => 'login_failed',
                            'model_type' => $user ? 'user' : 'guest',
                            'model_id' => $user->id ?? null,
                            'old_values' => null,
                            'new_values' => [
                                'credentials' => is_array($event->credentials) ? array_keys($event->credentials) : $event->credentials,
                                'ip' => request()?->ip(),
                                'user_agent' => request()?->userAgent(),
                            ],
                            'ip_address' => request()?->ip(),
                            'user_agent' => request()?->userAgent(),
                        ]);
                    } catch (\Exception $e) {
                        // ignore logging failure
                    }
                });

                // Listen for password reset events
                Event::listen(\Illuminate\Auth\Events\PasswordReset::class, function ($event) {
                    try {
                        $user = $event->user ?? Auth::user();
                        AuditLog::create([
                            'timestamp' => now(),
                            'user_id' => $user->id ?? Auth::id(),
                            'action' => 'password_reset',
                            'model_type' => 'user',
                            'model_id' => $user->id ?? null,
                            'old_values' => null,
                            'new_values' => null,
                            'ip_address' => request()?->ip(),
                            'user_agent' => request()?->userAgent(),
                        ]);
                    } catch (\Exception $e) {
                        // ignore logging failure
                    }
                });

                // Register model observer for all models in app/Models
                $modelsPath = app_path('Models');
                if (is_dir($modelsPath)) {
                    foreach (glob($modelsPath.'/*.php') as $file) {
                        $class = 'App\\Models\\'.pathinfo($file, PATHINFO_FILENAME);
                        if (class_exists($class) && is_subclass_of($class, \Illuminate\Database\Eloquent\Model::class)) {
                            if ($class !== AuditLog::class) {
                                $class::observe(AuditObserver::class);
                            }
                        }
                    }
                }

                // Register notification observer for specific models
                \App\Models\Exam::observe(NotificationObserver::class);
                \App\Models\Attendance::observe(NotificationObserver::class);
                \App\Models\Student::observe(NotificationObserver::class);
                \App\Models\Notice::observe(NotificationObserver::class);
                \App\Models\StudyMaterial::observe(NotificationObserver::class);
            }
        } catch (\Exception $e) {
            // schema not available or other boot-time issue; skip audit registration
        }
    }
}
