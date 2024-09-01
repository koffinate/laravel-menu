<?php

declare(strict_types=1);

namespace Kfn\Menu;

use Closure;
use Exception;
use Illuminate\Support\Fluent;
use Kfn\Menu\Enum\MenuType;

/**
 * @implements \Kfn\Menu\Contracts\GroupedMenu
 */
class FactoryX implements \Kfn\Menu\Contracts\GroupedMenu
{
    private static string $name;
    private static string $group;
    private static MenuItemAttribute|null $groupAttribute = null;
    private static Fluent|null $factory = null;
    private static string $childName;

    /**
     * @param  string|null  $name
     * @param  string|null  $group
     * @param  array  $groupAttribute
     */
    public function __construct(
        string|null $name = null,
        string|null $group = null,
        array $groupAttribute = []
    ) {
        static::$name = $name ?? 'main';
        static::$group = $group ?? 'Default';
        static::$groupAttribute = new MenuItemAttribute($groupAttribute);
        if (! static::$factory instanceof Fluent) {
            static::$factory = new Fluent();
        }
    }

    /**
     * @param  bool  $grouped
     * @param  bool  $resolvedOnly
     *
     * @return \Kfn\Menu\MenuCollection
     * @throws \Throwable
     */
    public function get(bool $grouped = true, bool $resolvedOnly = true): MenuCollection
    {
        try {
            if (static::$factory[static::$name] instanceof MenuCollection) {
                $menus = static::$factory[static::$name];

                if ($resolvedOnly) {
                    $menus = $menus->filter(fn ($m) => $m->resolve());
                }

                if ($grouped) {
                    $menus = $menus->groupBy('group');
                }

                return $menus;
            }
        } catch (Exception $e) {
            throw_if(app()->hasDebugModeEnabled(), $e);
            app('log')->error('failed on get menu factory\n', [
                'message' => $e->getMessage(),
                'traces' => $e->getTraceAsString(),
            ]);
        }

        return new MenuCollection();
    }

    /**
     * @param  string  $name
     * @param  string  $title
     * @param  array  $param
     * @param  array  $attribute
     * @param  string|null  $activeRoute
     * @param  array|null  $activeRouteParam
     * @param  \Closure|bool  $resolver
     * @param  bool  $hasChild
     *
     * @return static
     */
    public function route(
        string $name,
        string $title,
        array $param = [],
        array $attribute = [],
        string|null $activeRoute = null,
        array|null $activeRouteParam = null,
        Closure|bool $resolver = true,
        bool $hasChild = false
    ): static {
        return $this->add(
            type: MenuType::ROUTE,
            name: $name,
            title: $title,
            param: $param,
            attribute: $attribute,
            activeName: $activeRoute,
            activeParam: $activeRouteParam,
            resolver: $resolver,
        );
    }

    /**
     * @param  string  $name
     * @param  string  $title
     * @param  array  $param
     * @param  array  $attribute
     * @param  string|null  $activeUrl
     * @param  array|null  $activeUrlParam
     * @param  \Closure|bool  $resolver
     * @param  bool  $hasChild
     *
     * @return $this
     */
    public function url(
        string $name,
        string $title,
        array $param = [],
        array $attribute = [],
        string|null $activeUrl = null,
        array|null $activeUrlParam = null,
        Closure|bool $resolver = true,
        bool $hasChild = false
    ): static {
        return $this->add(
            type: MenuType::URL,
            name: $name,
            title: $title,
            param: $param,
            attribute: $attribute,
            activeName: $activeUrl,
            activeParam: $activeUrlParam,
            resolver: $resolver,
        );
    }

    /**
     * @param  \Kfn\Menu\Enum\MenuType  $type
     * @param  string  $name
     * @param  string  $title
     * @param  array  $param
     * @param  array  $attribute
     * @param  string|null  $activeName
     * @param  array|null  $activeParam
     * @param  \Closure|bool  $resolver
     * @param  bool  $hasChild
     *
     * @return static
     */
    public function add(
        MenuType $type,
        string $name,
        string $title,
        array $param = [],
        array $attribute = [],
        string|null $activeName = null,
        array|null $activeParam = null,
        Closure|bool $resolver = true,
        bool $hasChild = false
    ): static {
        $factory = static::getFactory();
        $factory->add(
            new MenuItem(
                type: $type,
                title: $title,
                name: $name,
                param: $param,
                attribute: $attribute,
                activeName: $activeName,
                activeParam: $activeParam,
                group: static::$group,
                groupAttribute: static::$groupAttribute,
                resolver: $resolver,
            )
        );

        return $this;
        // return new static(name: static::$name, group: static::$group);
    }

    /**
     * @return \Kfn\Menu\MenuCollection
     */
    private static function getFactory(): MenuCollection
    {
        if (! static::$factory[static::$name] instanceof MenuCollection) {
            static::$factory[static::$name] = new MenuCollection();
        }

        return static::$factory[static::$name];
    }
}
