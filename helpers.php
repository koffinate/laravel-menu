<?php

use Koffin\Menu\Enum\MenuType;

if (! function_exists('menus')) {
    /**
     * Menu instance.
     *
     * @param  ?string  $name
     * @param  ?string  $group
     * @return \Koffin\Menu\Contracts\Menu
     */
    function menus(?string $name = null, ?string $group = null): \Koffin\Menu\Contracts\Menu
    {
        return new \Koffin\Menu\Factory(name: $name, group: $group);
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
