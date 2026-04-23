<?php

namespace Kfn\Menu\Contracts;

use Closure;
use Kfn\Menu\Enum\MenuType;
use Kfn\Menu\MenuCollection;

interface GroupItem
{
    /**
     * @param  string  $name
     * @param  string  $title
     * @param  array  $param
     * @param  array  $attribute
     * @param  int  $sort
     * @param  string|null  $activeRoute
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
        string|null $activeRoute = null,
        array|null $activeRouteParam = null,
        MenuCollection|null $items = null,
        bool|Closure $resolver = true
    ): static;

    /**
     * @param  string  $name
     * @param  string  $title
     * @param  array  $param
     * @param  array  $attribute
     * @param  int  $sort
     * @param  string|null  $activeUrl
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
        string|null $activeUrl = null,
        array|null $activeUrlParam = null,
        MenuCollection|null $items = null,
        bool|Closure $resolver = true
    ): static;

    /**
     * @param  MenuType  $type
     * @param  string  $name
     * @param  string  $title
     * @param  array  $param
     * @param  array  $attribute
     * @param  int  $sort
     * @param  string|null  $activeName
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
        string|null $activeName = null,
        array|null $activeParam = null,
        MenuCollection|null $items = null,
        bool|Closure $resolver = true
    ): static;
}
