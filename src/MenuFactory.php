<?php

namespace Koffin\Core\Menu;

use ArrayObject;
use Spatie\Menu\Laravel\Facades\Menu;

class MenuFactory extends ArrayObject
{
    public function offsetSet(mixed $key, mixed $value): void
    {
        if ($value instanceof Menu) {
            parent::offsetSet($key, $value);
        }
        throw new \InvalidArgumentException('Value must be a Menu');
    }
}
