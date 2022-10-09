<?php

use Koffin\Menu\Menu;

if (!function_exists('menus')) {
    /**
     * @param null|string $menu
     * @return Menu
     */
    function menus(?string $menu = null, ?string $group = null): Menu
    {
        return app('menus', ['menu' => $menu, 'group' => $group]);
    }
}

if (!function_exists('menuType')) {
    /**
     * Menu Type Enum
     *
     * @return MenuType
     */
    function menuType(): string
    {
        return "\\Koffin\\Menu\\Enum\\MenuType";
    }
}
