<?php

use Kfn\Menu\Contracts\GroupedMenu;
use Kfn\Menu\Enum\MenuType;
use Kfn\Menu\Factory;
use Kfn\Menu\SubMenu;

if (! function_exists('menus')) {
    /**
     *  Menu instance.
     *
     * @param  string|null  $name
     *
     * @return GroupedMenu
     */
    function menus(string|null $name = null): GroupedMenu
    {
        return new Factory(name: $name);
    }
}

if (! function_exists('subMenus')) {
    /**
     *  Menu instance.
     *
     * @param  string|null  $name
     *
     * @return Kfn\Menu\Contracts\SubMenu
     */
    function subMenus(string|null $name = null): Kfn\Menu\Contracts\SubMenu
    {
        return new SubMenu(name: $name);
    }
}

if (! function_exists('menuType')) {
    /**
     * Menu Type Enum.
     *
     * @param  string|null  $type
     *
     * @return MenuType|string|null
     */
    function menuType(string|null $type = null): MenuType|string|null
    {
        if ($type) {
            return MenuType::tryFrom($type);
        }

        return MenuType::class;
    }
}
