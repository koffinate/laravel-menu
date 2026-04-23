<?php

namespace Kfn\Menu;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Kfn\Menu\Enum\MenuType;

/**
 * @implements Contracts\MenuItem
 *
 * @property MenuType $type
 * @property string $title
 * @property string $name
 * @property array $param
 * @property int $sort
 * @property string $href
 * @property MenuItemAttribute $attribute
 * @property string|null $activeName
 * @property array|null $activeParam
 * @property bool|\Closure $resolver
 * @property bool $hasChild
 */
class MenuItem implements Contracts\MenuItem
{
    /** @var MenuItemAttribute */
    public MenuItemAttribute $attribute;

    /** @var bool */
    final public bool $hasChild = false;

    /** @var string */
    public string $href = '#';

    /**
     * @param  MenuType  $type
     * @param  string  $title
     * @param  string  $name
     * @param  array  $param
     * @param  array|object  $attribute
     * @param  int  $sort
     * @param  array|string|null  $activeName
     * @param  array|null  $activeParam
     * @param  MenuCollection|null  $items
     * @param  bool|\Closure  $resolver
     */
    public function __construct(
        public readonly MenuType $type,
        public readonly string $title,
        public readonly string $name,
        public readonly array $param = [],
        array|object $attribute = [],
        public readonly int $sort = 0,
        public readonly array|string|null $activeName = null,
        public readonly array|null $activeParam = null,
        public MenuCollection|null $items = null,
        public readonly bool|\Closure $resolver = true
    ) {
        if (! $attribute instanceof MenuItemAttribute) {
            $attribute = new MenuItemAttribute($attribute);
        }
        $this->attribute = $attribute;
        $this->items ??= new MenuCollection;
        $this->hasChild = $this->items->isNotEmpty();
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
        $this->resolveHref();

        // resolve items
        if ($this->items->isNotEmpty()) {
            $this->items = $this->items->filter(fn (MenuItem $it) => $it->resolve());
        }

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
            if ($name->is('#')) {
                throw new Exception('hashed link');
            }

            $name = $name->toString();
            $this->href = match ($this->type) {
                MenuType::ROUTE => route($name, $params),
                MenuType::URL => url($name, $params),
                default => '#',
            };
        }
        catch (Exception $e) {
            $this->href = '#';
        }
    }

    /**
     * @param  array|string|null  $name
     * @param  array  $params
     *
     * @return bool
     */
    private function getActiveStatus(array|string|null $name = '', array $params = []): bool
    {
        $name = collect((array) $name)->flatMap(function ($nm) {
            $nm = preg_replace('/\s+|\h+/', '', $nm);
            if (! empty($nm)) {
                return $this->type === MenuType::ROUTE ? [$nm, $nm.'.*'] : [$nm, $nm.'/*'];
            }
        });
        if ($name->isEmpty()) {
            return false;
        }

        try {
            return match ($this->type) {
                MenuType::ROUTE => $this->getActiveByRoute($name->toArray(), $params),
                MenuType::URL => $this->getActiveByUrl($name->toArray()),
                default => false,
            };
        }
        catch (Exception $e) {
            app('log')->error($e->getMessage());
        }

        return false;
    }

    private function getActiveByUrl(array $names): bool
    {
        return request()->is($names);
    }

    private function getActiveByRoute(array $names, array $params = []): bool
    {
        if (request()->routeIs($names)) {
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
                    $requestRoute->parameter($key) instanceof Model
                    && $value instanceof Model
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
                    }
                    catch (Exception $e) {
                        return false;
                    }
                }
                else {
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
