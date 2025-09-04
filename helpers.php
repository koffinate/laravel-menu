<?php

use Kfn\Menu\Enum\MenuType;

if (! function_exists('menus')) {
    /**
     *  Menu instance.
     *
     * @param  string|null  $name
     *
     * @return \Kfn\Menu\Contracts\GroupedMenu
     */
    function menus(string|null $name = null): \Kfn\Menu\Contracts\GroupedMenu
    {
        return new \Kfn\Menu\Factory(name: $name);
    }
}

if (! function_exists('subMenus')) {
    /**
     *  Menu instance.
     *
     * @param  string|null  $name
     *
     * @return \Kfn\Menu\Contracts\SubMenu
     */
    function subMenus(string|null $name = null): \Kfn\Menu\Contracts\SubMenu
    {
        return new \Kfn\Menu\SubMenu(name: $name);
    }
}

if (! function_exists('menuType')) {
    /**
     * Menu Type Enum.
     *
     * @param  string|null  $type
     * @return MenuType|string|null
     */
    function menuType(?string $type = null): MenuType|string|null
    {
        if ($type) {
            return MenuType::tryFrom($type);
        }

        return MenuType::class;
    }
}
