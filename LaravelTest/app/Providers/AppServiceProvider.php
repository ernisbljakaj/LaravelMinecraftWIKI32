<?php

namespace App\Providers;

use App\Moderation\CommentModerator;
use App\Moderation\ModerationManager;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CommentModerator::class, fn () => (new ModerationManager)->driver());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.custom');

        RateLimiter::for('comments', function (Request $request) {
            return Limit::perMinute((int) config('moderation.quota.per_minute', 5))
                ->by($request->user()?->id ?: $request->ip());
        });
    }
}
