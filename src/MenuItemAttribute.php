<?php

namespace Koffin\Menu;

use Illuminate\Support\Fluent;
use Koffin\Menu\Enum\MenuType;

class MenuItemAttribute
{
    public string|null $icon = null;
    public string|array|null $styleClass = null;
    public string|array|null $tags = null;

    public function __construct(public Fluent|array $attributes)
    {
        if (is_array($this->attributes)) {
            $this->attributes = new Fluent($this->attributes);
        }
        $this->setAttribute($this->attributes);
    }

    private function setAttribute(Fluent $attributes): void
    {
        $this->icon = $attributes->icon ?? null;

        $this->styleClass = $attributes->styleClass ?? null;
        if (is_array($this->styleClass)) {
            $this->styleClass = implode(' ', $this->styleClass);
        }

        $this->tags = $attributes->tags ?? null;
        if (is_array($this->tags)) {
            $this->tags = implode(' ', $this->tags);
        }
    }
}
