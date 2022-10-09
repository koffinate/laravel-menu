<?php

namespace Koffin\Menu;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Koffin\Menu\Enum\MenuType;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('menus', function ($app, $param): Menu {
            $menu = $group = null;
            if(Arr::has($param, 'menu') && is_string($param['menu'])) {
                $menu = $param['menu'];
            }
            if(Arr::has($param, 'group') && is_string($param['group'])) {
                $group = $param['group'];
            }

            return new Menu(menu: $menu, group: $group);
        });
        // $this->app->alias(MenuType(), 'menuType');
    }
}
