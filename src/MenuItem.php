<?php

namespace Koffin\Menu;

use Exception;
use Koffin\Menu\Enum\MenuType;

class MenuItem
{
    public function __construct(
        public MenuType $type,
        public string $title,
        public array $attribute = [],
        public string $name,
        public array $param = [],
        public ?string $activeRoute = null,
        public string $group = 'Default',
        public \Closure|bool $resolver = true,
    )
    { }

    public function resolve(): bool
    {
        if($this->resolver instanceof \Closure) {
            return (bool) $this->resolver->call($this);
        }
        return $this->resolver;
    }

    public function isActive(): bool
    {
        if ($this->type == MenuType::Route) {
            return $this->isActiveRoute($this->activeRoute ?? $this->name, $this->param);
        }

        return false;
    }

    private function isActiveRoute(string $route = '', array $params = []): bool
    {
        if (empty($route = trim($route))) {
            return false;
        }

        try {
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
                        && $requestRoute->parameter($key)->id != $value->id
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
        } catch (Exception $e) {}

        return false;
    }
}
