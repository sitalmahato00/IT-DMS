<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Log all exceptions with context
            \Log::error('Exception occurred', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id() ?? null,
                'request_path' => request()->path(),
                'method' => request()->method(),
            ]);

            // Send critical errors to admin email
            if ($this->isCritical($e)) {
                \Mail::queue(new \App\Mail\ErrorAlertMail($e));
            }
        });

        $this->renderable(function (Throwable $e, $request) {
            // 404 errors
            if ($this->isHttpException($e) && $e->getStatusCode() == 404) {
                return response()->view('errors.404', [], 404);
            }

            // 429 rate limit errors
            if ($this->isHttpException($e) && $e->getStatusCode() == 429) {
                return response()->view('errors.429', [], 429);
            }

            // Production error pages
            if (!config('app.debug')) {
                return response()->view('errors.500', [], 500);
            }
        });
    }

    /**
     * Determine if the exception is critical and should trigger alerts
     */
    private function isCritical(Throwable $e): bool
    {
        $criticalExceptions = [
            \PDOException::class,
            \ErrorException::class,
        ];

        foreach ($criticalExceptions as $critical) {
            if ($e instanceof $critical) {
                return true;
            }
        }

        return false;
    }
}

