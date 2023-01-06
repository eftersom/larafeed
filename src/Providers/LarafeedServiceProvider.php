<?php

namespace Eftersom\Larafeed\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Eftersom\Larafeed\Models\User;
use Auth;

class LarafeedServiceProvider extends ServiceProvider
{
    /**
    * Register services.
    *
    * @return void
    */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/larafeed.php', 'larafeed'
        );
    }

    /**
     * Bootstrap.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        View::addNamespace('larafeed', __DIR__.'/../../resources/views');

        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'larafeed');

        $this->configureRoutes();

        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'larafeed');

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        }

        $this->configurePublishing();
        $this->registerAuth();
    }

    /**
     * Register package authentication model.
     *
     * @return void
     */
    private function registerAuth(): void
    {
        $this->app->config->set('auth.providers.users', [
            'driver' => 'eloquent',
            'model' => User::class,
        ]);
    }
    
    /**
     * Configure routes.
     *
     * @return void
     */
    private function configureRoutes(): void
    {
        Route::middleware(config('larafeed.middleware'))
             ->prefix(config('larafeed.path'))
             ->group(function () {
                 $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
             });
    }

    /**
     * Configure publishing.
     *
     * @return void
     */
    private function configurePublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../resources/views' => resource_path('views/vendor/larafeed'),
            ], 'larafeed-views');

            $this->publishes([
            __DIR__.'/../../lang' => $this->app->langPath('vendor/larafeed'),
            ]);

            $this->publishes([
                __DIR__.'/../../config/larafeed.php' => config_path('larafeed.php'),
            ], 'larafeed-config');

            $this->publishes([
                __DIR__.'/../../public' => public_path('vendor/larafeed'),
            ], 'larafeed-public');

            $this->publishes([
                __DIR__.'/../../database/migrations/' => database_path('migrations')
            ], 'larafeed-migrations');
        }
    }
}
