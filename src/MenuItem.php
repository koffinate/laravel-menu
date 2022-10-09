<?php

namespace Koffin\Menu;

use Koffin\Menu\Enum\MenuType;

class MenuItem
{
    public function __construct(
        public MenuType $type,
        public string $title,
        public array $attribute = [],
        public string $name,
        public array $param = [],
        public string $group = 'Default',
        public \Closure|bool $resolver = true,
        public bool $isActive = false,
    )
    { }

    public function resolve(): bool
    {
        if ($this->type == MenuType::Route) {
            $this->isActive = $this->activeRoute($this->name, $this->param);
        }

        if($this->resolver instanceof \Closure) {
            return (bool) $this->resolver->call($this);
        }
        return $this->resolver;
    }

    private function activeRoute(string $route = '', array $params = []): bool
    {
        if (empty($route = trim($route))) {
            return false;
        }

        if (request()->routeIs("{$route}*")) {
            if (empty($params)) {
                return true;
            }

            $requestRoute = request()->route();

            foreach ($params as $key => $value) {
                if (
                    $requestRoute->parameter($key) instanceof \Illuminate\Database\Eloquent\Model
                    && $value instanceof \Illuminate\Database\Eloquent\Model
                    && $requestRoute->parameter($key)->id != $value->id
                ) {
                    return false;
                }
                if ($requestRoute->parameter($key) != $value) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
