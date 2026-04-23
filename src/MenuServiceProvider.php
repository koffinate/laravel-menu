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
        $this->mergeConfigFrom(__DIR__.'/../config/menus.php', 'menus');
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

        $this->publishes([__DIR__.'/../config/menus.php' => config_path('menus.php')], 'kfn-config-menu');
        // $this->publishes([__DIR__.'/../config/menu-icon.php' => config_path('koffinate/menu-icon.php')], 'kfn-config-menu-icon');

        $this->app->booted(function () {
            $router = $this->app['router'];

            if ($router) {
                collect(config('menus.groups'))->each(function (array $group, string $name) {
                    collect($group['items'] ?? [])->each(function (string $menuClass) use ($name) {
                        if (class_exists($menuClass)) {
                            new $menuClass($name);
                        }
                    });
                });
            }
        });
    }
}
