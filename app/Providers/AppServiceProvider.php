<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
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
        Gate::define('admin-co-only', function (User $user) {
            return $user->role->id === 3
                ? Response::deny()
                : Response::allow();
        });

        Gate::define('co_leader-only', function (User $user) {
            return $user->role->id === 1
                ? Response::allow()
                : Response::deny();
        });
    }
}
