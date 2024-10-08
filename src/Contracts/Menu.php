<?php

namespace Kfn\Menu\Contracts;

use Closure;
use Illuminate\Support\Collection;
use Kfn\Menu\Enum\MenuType;

interface Menu
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function get(): Collection;

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
    ): static;

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
     * @return static
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
    ): static;

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
    ): static;
}
