<?php

namespace Kfn\Menu;

use Closure;
use Exception;
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
    private static Fluent|null $factory = null;

    /** @var string */
    public string $name;

    /** @var string */
    public string $title;

    /** @var int */
    public int $sort;

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
        string $name,
        string $title,
        array|object $attribute = [],
        int $sort = 0
    ) {
        if (!$attribute instanceof MenuItemAttribute) {
            $attribute = new MenuItemAttribute($attribute);
        }
        if (! static::$factory instanceof Fluent) {
            static::$factory = new Fluent();
        }
        static::$collectionName = $name;

        $this->name = $name;
        $this->title = $title;
        $this->sort = $sort;
        $this->attribute = $attribute;
        $this->items = static::getItems();
    }

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
            resolver: $resolver,
        );
    }

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
            resolver: $resolver,
        );
    }

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





    // /**
    //  * @return string
    //  */
    // public function getHref(): string
    // {
    //     return $this->href;
    // }
    //
    // /**
    //  * @return bool
    //  */
    // public function resolve(): bool
    // {
    //     if ($this->resolver instanceof \Closure) {
    //         return (bool) $this->resolver->call($this);
    //     }
    //
    //     return $this->resolver;
    // }
    //
    // /**
    //  * @return bool
    //  */
    // public function isActive(): bool
    // {
    //     return $this->getActiveStatus($this->activeName ?? $this->name, $this->activeParam ?? $this->param);
    // }
    //
    // /**
    //  * @return void
    //  */
    // private function resolveHref(): void
    // {
    //     $params = $this->param;
    //     $name = str($this->name)->trim();
    //
    //     try {
    //         if ($name->isEmpty()) {
    //             throw new Exception("Menu item attribute name is empty");
    //         }
    //
    //         $name = $name->toString();
    //         $this->href = match ($this->type) {
    //             MenuType::ROUTE => route($name, $params),
    //             MenuType::URL => url($name, $params),
    //             default => '#',
    //         };
    //     } catch (Exception $e) {
    //         $this->href = '#';
    //     }
    // }
    //
    // /**
    //  * @param  string|null  $name
    //  * @param  array  $params
    //  *
    //  * @return bool
    //  */
    // private function getActiveStatus(string|null $name = '', array $params = []): bool
    // {
    //     $name = str($name)->trim();
    //     if ($name->isEmpty()) {
    //         return false;
    //     }
    //
    //     try {
    //         $name = $name->toString();
    //
    //         return match ($this->type) {
    //             MenuType::ROUTE => $this->getActiveByRoute($name, $params),
    //             MenuType::URL => $this->getActiveByUrl($name),
    //             default => false,
    //         };
    //
    //     } catch (Exception $e) {
    //         app('log')->error($e->getMessage());
    //     }
    //
    //     return false;
    // }
    //
    // private function getActiveByUrl(string $name): bool
    // {
    //     return request()->is($name, "{$name}.*");
    // }
    //
    // private function getActiveByRoute(string $name, array $params = []): bool
    // {
    //     if (request()->routeIs($name, "{$name}.*")) {
    //         if (empty($params)) {
    //             return true;
    //         }
    //
    //         $requestRoute = request()->route();
    //         $paramNames = $params; // $requestRoute->parameterNames();
    //
    //         foreach ($params as $key => $value) {
    //             if (is_int($key)) {
    //                 $key = $paramNames[$key];
    //             }
    //
    //             if (
    //                 $requestRoute->parameter($key) instanceof \Illuminate\Database\Eloquent\Model
    //                 && $value instanceof \Illuminate\Database\Eloquent\Model
    //                 && $requestRoute->parameter($key)->id != ($value->id ?? null)
    //             ) {
    //                 return false;
    //             }
    //
    //             if (is_object($requestRoute->parameter($key))) {
    //                 // try to check param is enum type
    //                 try {
    //                     if ($requestRoute->parameter($key)->value && $requestRoute->parameter($key)->value != $value) {
    //                         return false;
    //                     }
    //                 } catch (Exception $e) {
    //                     return false;
    //                 }
    //             } else {
    //                 if ($requestRoute->parameter($key) != $value) {
    //                     return false;
    //                 }
    //             }
    //         }
    //
    //         return true;
    //     }
    //
    //     return false;
    // }
}
