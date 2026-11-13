<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request): Limit {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request): Limit {
            $key = strtolower((string) $request->input('username')).'|'.$request->ip();

            return Limit::perMinute(5)->by(hash('sha256', $key));
        });

        RateLimiter::for('password-reset', function (Request $request): Limit {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('email-verification', function (Request $request): Limit {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        ResetPassword::createUrlUsing(function (User $user, string $token): string {
            return url('/reset-password/' . $token . '?email=' . urlencode($user->email));
        });

        VerifyEmail::createUrlUsing(function (User $user): string {
            return URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(config('auth.verification.expire')),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );
        });
    }
}
