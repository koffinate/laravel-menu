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
        $this->mergeConfigFrom(__DIR__.'/../config/menu-icon.php', 'koffinate.menu-icon');
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

        $this->publishes([__DIR__.'/../config/menu-icon.php' => config_path('koffinate/menu-icon.php')], 'kfn-config-menu');
    }
}
