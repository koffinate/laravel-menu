<?php

namespace Koffin\Menu;

use Koffin\Menu\Enum\MenuType;

class MenuItem
{
    public function __construct(
        public MenuType $type,
        public string $name,
        public string $title,
        public array $param = [],
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
}
