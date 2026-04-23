<?php

namespace Kfn\Menu;

use Closure;
use Illuminate\Support\Fluent;
use Kfn\Menu\Enum\MenuType;

/**
 * @implements Contracts\GroupItem
 *
 * @property MenuType $type
 * @property string $title
 * @property string $name
 * @property array $param
 * @property string $href
 * @property MenuItemAttribute $attribute
 * @property string|null $activeName
 * @property array|null $activeParam
 * @property string $group
 * @property MenuItemAttribute $groupAttribute
 * @property bool|Closure $resolver
 * @property bool $hasChild
 */
class GroupItem implements Contracts\GroupItem
{
    /** @var string */
    private string $collectionName;

    /** @var Fluent|null */
    private Fluent|null $factory = null;

    /** @var MenuItemAttribute */
    public MenuItemAttribute $attribute;

    /** @var MenuCollection<MenuItem> */
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
        if (! $this->factory instanceof Fluent) {
            $this->factory = new Fluent;
        }
        $this->collectionName = $name;

        $this->attribute = $attribute;
        $this->items = $this->getItems();
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
        $factory = $this->getItems();
        $itemName = str($type->value.'_'.$name)->snake()->slug('_')->toString();

        if (! $factory->has($itemName)) {
            $factory->put(
                $itemName,
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
        }

        return $this;
    }

    /**
     * @return MenuCollection
     */
    private function getItems(): MenuCollection
    {
        if (! $this->factory[$this->collectionName] instanceof MenuCollection) {
            $this->factory[$this->collectionName] = new MenuCollection;
        }

        return $this->factory[$this->collectionName];
    }
}
