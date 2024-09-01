<?php

declare(strict_types=1);

namespace Kfn\Menu;

use Illuminate\Support\Fluent;

/**
 * @implements \Kfn\Menu\Contracts\GroupedMenu
 */
class Factory implements \Kfn\Menu\Contracts\GroupedMenu
{
    /** @var string */
    private static string $name;

    /** @var \Illuminate\Support\Fluent|null */
    private static Fluent|null $factory = null;

    /**
     * @param  string|null  $name
     */
    public function __construct(
        string|null $name = null
    ) {
        static::$name = $name ?: 'main';
        if (! static::$factory instanceof Fluent) {
            static::$factory = new Fluent();
        }
    }

    /**
     * Add GroupItem.
     *
     * @param  string  $name
     * @param  string  $title
     * @param  array|object  $attributes
     * @param  int  $sort
     *
     * @return static
     */
    public function add(
        string $name,
        string $title,
        object|array $attributes = [],
        int $sort = 0
    ): static {
        if (! static::$factory[static::$name] instanceof GroupedMenu) {
            static::$factory[static::$name] = new GroupedMenu(
                name: $name,
                title: $title,
                attributes: $attributes
            );
        }

        return $this;
    }

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
    ): GroupedMenu|GroupItem {
        $groupedMenu = static::$factory[static::$name];

        if ($groupName) {
            $groupedMenu = $groupedMenu->get($groupName);
        }

        if ($groupedMenu instanceof GroupedMenu && $resolvedOnly) {
            $groupedMenu = $groupedMenu->each(function (GroupItem $group) {
                $groupItems = $group->items->filter(fn ($it) => $it->resolve());
                $group->items = $groupItems;

                return $group;
            });
        }

        // throw_if(app()->hasDebugModeEnabled(), $e);
        // app('log')->error('failed on get menu factory\n', [
        //     'message' => $e->getMessage(),
        //     'traces' => $e->getTraceAsString(),
        // ]);

        return $groupedMenu;
    }
}
