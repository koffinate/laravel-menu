<?php

namespace Kfn\Menu;

use Closure;
use Illuminate\Support\Fluent;
use Kfn\Menu\Enum\MenuType;

/**
 * @implements \Kfn\Menu\Contracts\GroupItem
 *
 * @property  \Kfn\Menu\Enum\MenuType  $type
 * @property  string  $title
 * @property  string $name
 * @property  array  $param
 * @property  string $href
 * @property  \Kfn\Menu\MenuItemAttribute  $attribute
 * @property  string|null  $activeName
 * @property  array|null  $activeParam
 * @property  string  $group
 * @property  \Kfn\Menu\MenuItemAttribute  $groupAttribute
 * @property  \Closure|bool  $resolver
 * @property  bool  $hasChild
 */
class GroupItem implements \Kfn\Menu\Contracts\GroupItem
{
    /** @var string */
    private static string $collectionName;

    /** @var \Illuminate\Support\Fluent|null */
    private static ?Fluent $factory = null;

    /** @var \Kfn\Menu\MenuItemAttribute */
    public MenuItemAttribute $attribute;

    /** @var \Kfn\Menu\MenuCollection<\Kfn\Menu\MenuItem> */
    public MenuCollection $items;

    /**
     * @param  string  $name
     * @param  string  $title
     * @param  array|object  $attribute
     * @param  int  $sort
     */
    public function __construct(
        public string $name = 'default',
        public string $title = 'Default',
        array|object $attribute = [],
        public int $sort = 0
    ) {
        if (! $attribute instanceof MenuItemAttribute) {
            $attribute = new MenuItemAttribute($attribute);
        }
        if (! static::$factory instanceof Fluent) {
            static::$factory = new Fluent();
        }
        static::$collectionName = $name;

        $this->attribute = $attribute;
        $this->items = static::getItems();
    }

    /**
     * @param  string  $name
     * @param  string  $title
     * @param  array  $param
     * @param  array  $attribute
     * @param  int  $sort
     * @param  string|array|null  $activeRoute
     * @param  array|null  $activeRouteParam
     * @param  MenuCollection|null  $items
     * @param  Closure|bool  $resolver
     *
     * @return $this
     */
    public function route(
        string $name,
        string $title,
        array $param = [],
        array $attribute = [],
        int $sort = 0,
        string|array|null $activeRoute = null,
        array|null $activeRouteParam = null,
        MenuCollection|null $items = null,
        Closure|bool $resolver = true
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
     * @param  string|array|null  $activeUrl
     * @param  array|null  $activeUrlParam
     * @param  MenuCollection|null  $items
     * @param  Closure|bool  $resolver
     *
     * @return $this
     */
    public function url(
        string $name,
        string $title,
        array $param = [],
        array $attribute = [],
        int $sort = 0,
        string|array|null $activeUrl = null,
        array|null $activeUrlParam = null,
        MenuCollection|null $items = null,
        Closure|bool $resolver = true
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
     * @param  string|array|null  $activeName
     * @param  array|null  $activeParam
     * @param  MenuCollection|null  $items
     * @param  Closure|bool  $resolver
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
        string|array|null $activeName = null,
        array|null $activeParam = null,
        MenuCollection|null $items = null,
        Closure|bool $resolver = true
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
     * @return \Kfn\Menu\MenuCollection
     */
    private static function getItems(): MenuCollection
    {
        if (! static::$factory[static::$collectionName] instanceof MenuCollection) {
            static::$factory[static::$collectionName] = new MenuCollection();
        }

        return static::$factory[static::$collectionName];
    }
}
