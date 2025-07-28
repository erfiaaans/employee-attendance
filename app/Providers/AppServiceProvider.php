<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Observers\UserObserver;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Carbon::setLocale('id');
    }
}
