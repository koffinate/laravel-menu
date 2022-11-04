<?php

declare(strict_types=1);

namespace Koffin\Menu;

use Closure;
use Exception;
use Illuminate\Support\Collection;
use Koffin\Menu\Enum\MenuType;

class Menu
{
    public static string $menu = 'mainMenu';
    public static string $group = 'Default';
    public static array $factory;

    public function __construct(?string $menu = null, ?string $group = null)
    {
        if ($menu) {
            static::$menu = $menu;
        }
        if ($group) {
            static::$group = $group;
        }
    }

    public function get(): Collection
    {
        try {
            if (static::$factory[static::$menu]) {
                return collect(static::$factory[static::$menu])->groupBy('group');
            }
        } catch (Exception $e) {}

        return collect();
    }

    public static function route(string $name, string $title, array $attribute = [], array $param = [], ?string $activeRoute = null, Closure|bool $resolver = true): static
    {
        return static::add(
            type: MenuType::ROUTE,
            name: $name,
            title: $title,
            attribute: $attribute,
            param: $param,
            activeRoute: $activeRoute,
            resolver: $resolver,
        );
    }

    public static function url(string $name, string $title, array $attribute = [], array $param = [], ?string $activeRoute = null, Closure|bool $resolver = true): static
    {
        return static::add(
            type: MenuType::URL,
            name: $name,
            title: $title,
            attribute: $attribute,
            param: $param,
            activeRoute: $activeRoute,
            resolver: $resolver,
        );
    }

    public static function add(MenuType $type, string $name, string $title, array $attribute = [], array $param = [], ?string $activeRoute = null, Closure|bool $resolver = true): static
    {
        $factory = static::getFactory();
        $factory->add(
            new MenuItem(
                type: $type,
                name: $name,
                title: $title,
                attribute: $attribute,
                param: $param,
                activeRoute:$activeRoute,
                group: static::$group,
                resolver: $resolver,
            )
        );
        return new static();
    }

    private static function getFactory(): Collection
    {
        if (empty(static::$factory[static::$menu]) || static::$factory[static::$menu] instanceof Collection == false) {
            static::$factory[static::$menu] = collect();
        }
        return static::$factory[static::$menu];
    }
}
