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
    function menus(string|null $name = null)
    {
        return new \Kfn\Menu\Factory(name: $name);
    }
}

if (! function_exists('menuType')) {
    /**
     * Menu Type Enum.
     *
     * @param  string|null  $type
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
