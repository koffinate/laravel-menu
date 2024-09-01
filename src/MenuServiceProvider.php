<?php

namespace Kfn\Menu;

use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->singleton('menus', fn ($app, $p) => new Factory(name: $p['name'] ?: null));
        $this->app->alias('menus', Factory::class);
    }
}
