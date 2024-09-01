<?php

namespace Kfn\Menu;

use Exception;
use Kfn\Menu\Enum\MenuType;

/**
 * @implements \Kfn\Menu\Contracts\MenuItem
 *
 * @property  \Kfn\Menu\Enum\MenuType  $type
 * @property  string  $title
 * @property  string $name
 * @property  array  $param
 * @property  int $sort
 * @property  string $href
 * @property  \Kfn\Menu\MenuItemAttribute  $attribute
 * @property  string|null  $activeName
 * @property  array|null  $activeParam
 * @property  \Closure|bool  $resolver
 * @property  bool  $hasChild
 */
class MenuItem implements \Kfn\Menu\Contracts\MenuItem
{
    /** @var \Kfn\Menu\MenuItemAttribute */
    public MenuItemAttribute $attribute;

    /** @var string */
    public string $href = '#';

    /** @var \Kfn\Menu\MenuCollection|null */
    public MenuCollection|null $items = null;

    /**
     * @param  \Kfn\Menu\Enum\MenuType  $type
     * @param  string  $title
     * @param  string  $name
     * @param  array  $param
     * @param  array|object  $attribute
     * @param  int  $sort
     * @param  string|null  $activeName
     * @param  array|null  $activeParam
     * @param  \Closure|bool  $resolver
     */
    public function __construct(
        readonly public MenuType $type,
        readonly public string $title,
        readonly public string $name,
        readonly public array $param = [],
        array|object $attribute = [],
        readonly public int $sort = 0,
        readonly public string|null $activeName = null,
        readonly public array|null $activeParam = null,
        readonly public \Closure|bool $resolver = true
    ) {
        if (! $attribute instanceof MenuItemAttribute) {
            $attribute = new MenuItemAttribute($attribute);
        }

        $this->attribute = $attribute;
        $this->items = new MenuCollection();
        $this->resolveHref();
    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @return bool
     */
    public function resolve(): bool
    {
        if ($this->resolver instanceof \Closure) {
            return (bool) $this->resolver->call($this);
        }

        return $this->resolver;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getActiveStatus($this->activeName ?? $this->name, $this->activeParam ?? $this->param);
    }

    /**
     * @return void
     */
    private function resolveHref(): void
    {
        $params = $this->param;
        $name = str($this->name)->trim();

        try {
            if ($name->isEmpty()) {
                throw new Exception('Menu item attribute name is empty');
            }

            $name = $name->toString();
            $this->href = match ($this->type) {
                MenuType::ROUTE => route($name, $params),
                MenuType::URL => url($name, $params),
                default => '#',
            };
        } catch (Exception $e) {
            $this->href = '#';
        }
    }

    /**
     * @param  string|null  $name
     * @param  array  $params
     *
     * @return bool
     */
    private function getActiveStatus(string|null $name = '', array $params = []): bool
    {
        $name = str($name)->trim();
        if ($name->isEmpty()) {
            return false;
        }

        try {
            $name = $name->toString();

            return match ($this->type) {
                MenuType::ROUTE => $this->getActiveByRoute($name, $params),
                MenuType::URL => $this->getActiveByUrl($name),
                default => false,
            };
        } catch (Exception $e) {
            app('log')->error($e->getMessage());
        }

        return false;
    }

    private function getActiveByUrl(string $name): bool
    {
        return request()->is($name, "{$name}.*");
    }

    private function getActiveByRoute(string $name, array $params = []): bool
    {
        if (request()->routeIs($name, "{$name}.*")) {
            if (empty($params)) {
                return true;
            }

            $requestRoute = request()->route();
            $paramNames = $params; // $requestRoute->parameterNames();

            foreach ($params as $key => $value) {
                if (is_int($key)) {
                    $key = $paramNames[$key];
                }

                if (
                    $requestRoute->parameter($key) instanceof \Illuminate\Database\Eloquent\Model
                    && $value instanceof \Illuminate\Database\Eloquent\Model
                    && $requestRoute->parameter($key)->id != ($value->id ?? null)
                ) {
                    return false;
                }

                if (is_object($requestRoute->parameter($key))) {
                    // try to check param is enum type
                    try {
                        if ($requestRoute->parameter($key)->value && $requestRoute->parameter($key)->value != $value) {
                            return false;
                        }
                    } catch (Exception $e) {
                        return false;
                    }
                } else {
                    if ($requestRoute->parameter($key) != $value) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }
}
