<?php

namespace Koffin\Menu;

use Closure;
use Illuminate\Support\Collection;
use Koffin\Menu\Enum\MenuType;

class Menu
{
    public static string $menu = 'mainMenu';
    public static string $group = 'Default';
    public static array $factory;

    public function __construct(?string $menu = null, ?string $group = null)
    {
        if (!empty($menu)) {
            static::$menu = $menu;
        }
        if (!empty($group)) {
            static::$group = $group;
        }
    }

    public function get(): Collection
    {
        return collect(static::$factory[static::$menu])->groupBy('group');
    }

    public static function route(string $name, string $title, array $param = [], Closure|bool $resolver = true): static
    {
        return static::add(type: MenuType::Route, name: $name, title: $title, param: $param, resolver: $resolver);
    }

    public static function url(string $name, string $title, array $param = [], Closure|bool $resolver = true): static
    {
        return static::add(type: MenuType::URL, name: $name, title: $title, param: $param, resolver: $resolver);
    }

    public static function add(MenuType $type, string $name, string $title, array $param = [], Closure|bool $resolver = true): static
    {
        $factory = static::getFactory();
        $factory->add(
            new MenuItem(type: $type, name: $name, title: $title, param: $param, group: static::$group, resolver: $resolver)
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
