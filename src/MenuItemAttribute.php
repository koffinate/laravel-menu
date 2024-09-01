<?php

namespace Kfn\Menu;

use Illuminate\Support\Fluent;

/**
 * @extends \Illuminate\Support\Fluent
 *
 * @property string $icon
 * @property string $class
 * @property string $style
 * @property string $tags
 */
class MenuItemAttribute extends Fluent
{
    /**
     * @param  array|object  $attributes
     */
    public function __construct(array|object $attributes)
    {
        parent::__construct($attributes);
        $this->setAttribute();
    }

    /**
     * @return void
     */
    private function setAttribute(): void
    {
        $icon = $this->get('icon');
        if ($icon && (is_array($icon) || is_object($icon))) {
            $this->offsetSet('icon', implode(' ', (array)$icon));
        }

        $cssClass = $this->get('class');
        if ($cssClass && (is_array($cssClass) || is_object($cssClass))) {
            $cssClass = implode(' ', (array)$cssClass);
            $this->offsetSet('class', $cssClass);
        }

        $cssStyle = $this->get('style');
        if ($cssStyle && (is_array($cssStyle) || is_object($cssStyle))) {
            $cssStyle = implode(';', (array)$cssStyle);
            $this->offsetSet('style', $cssStyle);
        }

        $tags = $this->get('tags');
        if ($tags && (is_array($tags) || is_object($tags))) {
            $tags = implode(' ', (array)$tags);
            $this->offsetSet('tags', $tags);
        }
    }
}
