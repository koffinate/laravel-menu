<?php

use Koffin\Menu\Enum\MenuType;
use Koffin\Menu\Menu;

if (!function_exists('menus')) {
    /**
     * Menu instance
     *
     * @param ?string $menu
     * @param ?string $group
     *
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
     * @return ?MenuType
     */
    function menuType(?string $type): ?MenuType
    {
        if ($type) {
            MenuType::tryFrom($type);
        }
        return new MenuType;
    }
}
