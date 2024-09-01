<?php

namespace Kfn\Menu\Contracts;

interface GroupedMenu
{
    /**
     * Add GroupItem.
     *
     * @param  string  $name
     * @param  string  $title
     * @param  array|object  $attributes
     *
     * @return static
     */
    public function add(
        string $name,
        string $title,
        array|object $attributes = [],
    ): static;

    /**
     * Get Grouped Menu Collection.
     *
     * @param  string|null  $groupName
     * @param  bool  $resolvedOnly
     *
     * @return \Kfn\Menu\GroupedMenu|\Kfn\Menu\GroupItem
     */
    public function get(
        string|null $groupName = null,
        bool $resolvedOnly = true
    ): \Kfn\Menu\GroupedMenu|\Kfn\Menu\GroupItem;
}
