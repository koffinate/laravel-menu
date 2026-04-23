<?php

namespace Kfn\Menu;

use Illuminate\Support\Fluent;

/**
 * @extends Fluent
 *
 * @property string $icon
 * @property string $class
 * @property string $style
 * @property string $tags
 */
class MenuItemAttribute extends Fluent
{
    private string $menuTheme = 'default';

    /**
     * @param  array|object  $attributes
     */
    public function __construct(array|object $attributes)
    {
        parent::__construct($attributes);
        $this->menuTheme = config('menus.theme', 'default');
        $this->setAttribute();
    }

    /**
     * @return void
     */
    private function setAttribute(): void
    {
        $icon = $this->get('icon');
        $icons = [];
        if ($icon && (is_array($icon) || is_object($icon))) {
            foreach ((array) $icon as $_icon) {
                $icons[] = $this->getIcon($_icon);
            }
            $this->offsetSet('icon', implode(' ', $icons));
        }
        else {
            $this->offsetSet('icon', $this->getIcon($icon));
        }

        $cssClass = $this->get('class');
        if ($cssClass && (is_array($cssClass) || is_object($cssClass))) {
            $cssClass = implode(' ', (array) $cssClass);
            $this->offsetSet('class', $cssClass);
        }

        $cssStyle = $this->get('style');
        if ($cssStyle && (is_array($cssStyle) || is_object($cssStyle))) {
            $cssStyle = implode(';', (array) $cssStyle);
            $this->offsetSet('style', $cssStyle);
        }

        $tags = $this->get('tags');
        if ($tags && (is_array($tags) || is_object($tags))) {
            $tags = implode(' ', (array) $tags);
            $this->offsetSet('tags', $tags);
        }
        unset($icons, $icon, $cssClass, $cssStyle, $tags);
    }

    private function getIcon(string|null $icon): string|null
    {
        $_icon = null;

        if ($icon) {
            if ($this->menuTheme !== 'default') {
                $_icon = config("menus.themes.{$this->menuTheme}.icons.{$icon}");
            }

            if (empty($_icon)) {
                $_icon = config("menus.icons.{$icon}");
            }

            if (empty($_icon)) {
                $_icon = config("koffinate.menu-icon.{$icon}");
            }
        }

        return $_icon ?: $icon;
    }
}
