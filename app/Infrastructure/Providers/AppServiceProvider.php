<?php

namespace App\Domain\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\URL;

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
        URL::forceHttps(app()->environment('production'));

        Model::shouldBeStrict(!app()->environment('production'));

        DB::prohibitDestructiveCommands(app()->environment('production'));
    }
}
