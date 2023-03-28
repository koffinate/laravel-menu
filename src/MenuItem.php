<?php

namespace Koffin\Menu;

use Exception;
use Koffin\Menu\Enum\MenuType;

class MenuItem implements \Koffin\Menu\Contracts\MenuItem
{
    private ?MenuCollection $child = null;

    public function __construct(
        public MenuType $type,
        public string $title,
        public string $name,
        public array $param = [],
        public MenuItemAttribute|array $attribute = [],
        public ?string $activeRoute = null,
        public ?array $activeRouteParam = null,
        public string $group = 'Default',
        public MenuItemAttribute|array $groupAttribute = [],
        public \Closure|bool $resolver = true,
        public bool $hasChild = false,
    ) {
        if (is_array($this->attribute)) {
            $this->attribute = new MenuItemAttribute($this->attribute);
        }
        if (is_array($this->groupAttribute)) {
            $this->groupAttribute = new MenuItemAttribute($this->groupAttribute);
        }
    }

    public function resolve(): bool
    {
        if ($this->resolver instanceof \Closure) {
            return (bool) $this->resolver->call($this);
        }

        return $this->resolver;
    }

    public function isActive(): bool
    {
        if ($this->type == MenuType::ROUTE) {
            return $this->isActiveRoute($this->activeRoute ?? $this->name, $this->activeRouteParam ?? $this->param);
        }

        return false;
    }

    private function isActiveRoute(string $route = '', array $params = []): bool
    {
        $route = str($route)->trim();
        if ($route->isEmpty()) {
            return false;
        }

        try {
            $route = $route->toString();
            if (request()->routeIs($route, "{$route}.*")) {
                if (empty($params)) {
                    return true;
                }

                $requestRoute = request()->route();
                $paramNames = $requestRoute->parameterNames();

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
        } catch (Exception $e) {
        }

        return false;
    }
}
