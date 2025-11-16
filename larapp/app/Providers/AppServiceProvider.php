<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Regions;
use App\Models\Mosque;
use App\Policies\RegionPolicy;
use App\Policies\MosquePolicy;

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
        // Register model policies for Region and Mosque authorization
        Gate::policy(Regions::class, RegionPolicy::class);
        Gate::policy(Mosque::class, MosquePolicy::class);
    }
}
