<?php

declare(strict_types=1);

namespace Kfn\Menu;

use Closure;
use Illuminate\Support\Fluent;
use Kfn\Menu\Contracts\GroupedMenu;
use Kfn\Menu\Enum\MenuType;

/**
 * @implements GroupedMenu
 */
class SubMenu implements Contracts\SubMenu
{
    /** @var string */
    private static string $collectionName;

    /** @var Fluent|null */
    private static Fluent|null $factory = null;

    /** @var MenuCollection<MenuItem> */
    private MenuCollection $items;

    /**
     * @param  string  $name
     * @param  string  $title
     * @param  array|object  $attribute
     * @param  int  $sort
     */
    public function __construct(
        public string|null $name = null
    ) {
        if (! static::$factory instanceof Fluent) {
            static::$factory = new Fluent;
        }
        static::$collectionName = $name ?? uniqid('sub-menu-');
        $this->items = static::getItems();
    }

    /**
     * @return MenuCollection
     */
    public function all(): MenuCollection
    {
        return $this->get();
    }

    /**
     * @return MenuCollection
     */
    public function get(): MenuCollection
    {
        return $this->items;
    }

    /**
     * @param  string  $name
     * @param  string  $title
     * @param  array  $param
     * @param  array  $attribute
     * @param  int  $sort
     * @param  array|string|null  $activeRoute
     * @param  array|null  $activeRouteParam
     * @param  MenuCollection|null  $items
     * @param  bool|Closure  $resolver
     *
     * @return $this
     */
    public function route(
        string $name,
        string $title,
        array $param = [],
        array $attribute = [],
        int $sort = 0,
        array|string|null $activeRoute = null,
        array|null $activeRouteParam = null,
        MenuCollection|null $items = null,
        bool|Closure $resolver = true
    ): static {
        return $this->add(
            type: MenuType::ROUTE,
            name: $name,
            title: $title,
            param: $param,
            attribute: $attribute,
            sort: $sort,
            activeName: $activeRoute,
            activeParam: $activeRouteParam,
            items: $items,
            resolver: $resolver,
        );
    }

    /**
     * @param  string  $name
     * @param  string  $title
     * @param  array  $param
     * @param  array  $attribute
     * @param  int  $sort
     * @param  array|string|null  $activeUrl
     * @param  array|null  $activeUrlParam
     * @param  MenuCollection|null  $items
     * @param  bool|Closure  $resolver
     *
     * @return $this
     */
    public function url(
        string $name,
        string $title,
        array $param = [],
        array $attribute = [],
        int $sort = 0,
        array|string|null $activeUrl = null,
        array|null $activeUrlParam = null,
        MenuCollection|null $items = null,
        bool|Closure $resolver = true
    ): static {
        return $this->add(
            type: MenuType::URL,
            name: $name,
            title: $title,
            param: $param,
            attribute: $attribute,
            sort: $sort,
            activeName: $activeUrl,
            activeParam: $activeUrlParam,
            items: $items,
            resolver: $resolver,
        );
    }

    /**
     * @param  MenuType  $type
     * @param  string  $name
     * @param  string  $title
     * @param  array  $param
     * @param  array  $attribute
     * @param  int  $sort
     * @param  array|string|null  $activeName
     * @param  array|null  $activeParam
     * @param  MenuCollection|null  $items
     * @param  bool|Closure  $resolver
     *
     * @return $this
     */
    public function add(
        MenuType $type,
        string $name,
        string $title,
        array $param = [],
        array $attribute = [],
        int $sort = 0,
        array|string|null $activeName = null,
        array|null $activeParam = null,
        MenuCollection|null $items = null,
        bool|Closure $resolver = true
    ): static {
        $factory = static::getItems();
        $factory->add(
            new MenuItem(
                type: $type,
                title: $title,
                name: $name,
                param: $param,
                attribute: $attribute,
                sort: $sort,
                activeName: $activeName,
                activeParam: $activeParam,
                items: $items,
                resolver: $resolver,
            )
        );

        return $this;
    }

    /**
     * @return MenuCollection
     */
    private static function getItems(): MenuCollection
    {
        if (! static::$factory[static::$collectionName] instanceof MenuCollection) {
            static::$factory[static::$collectionName] = new MenuCollection;
        }

        return static::$factory[static::$collectionName];
    }
}
