<?php

namespace Kfn\Menu\Contracts;

use Closure;
use Kfn\Menu\Enum\MenuType;

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
     * @param  \Closure|bool  $resolver
     *
     * @return static
     */
    public function route(
        string $name,
        string $title,
        array $param = [],
        array $attribute = [],
        int $sort = 0,
        string|null $activeRoute = null,
        array|null $activeRouteParam = null,
        Closure|bool $resolver = true
    ): static;

    /**
     * @param  string  $name
     * @param  string  $title
     * @param  array  $param
     * @param  array  $attribute
     * @param  int  $sort
     * @param  string|null  $activeUrl
     * @param  array|null  $activeUrlParam
     * @param  \Closure|bool  $resolver
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
        Closure|bool $resolver = true
    ): static;

    /**
     * @param  \Kfn\Menu\Enum\MenuType  $type
     * @param  string  $name
     * @param  string  $title
     * @param  array  $param
     * @param  array  $attribute
     * @param  int  $sort
     * @param  string|null  $activeName
     * @param  array|null  $activeParam
     * @param  \Closure|bool  $resolver
     *
     * @return static
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
        Closure|bool $resolver = true
    ): static;
}
