<?php

declare(strict_types=1);

namespace Kfn\Menu;

use Exception;
use Illuminate\Support\Fluent;
use Throwable;

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
        string|null $name = null,
    ) {
        static::$name = $name ?: 'main';
        if (!static::$factory instanceof Fluent) {
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
        int $sort = 0,
    ): static {
        if (!static::$factory[static::$name] instanceof GroupedMenu) {
            static::$factory[static::$name] = new GroupedMenu();
        }
        if (!static::$factory[static::$name]->has($name)) {
            static::$factory[static::$name]->add([
                'name' => $name,
                'title' => $title,
                'attributes' => $attributes,
                'sort' => $sort,
            ]);
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
     * @throws \Throwable
     */
    public function get(
        string|null $groupName = null,
        bool $resolvedOnly = true,
    ): GroupedMenu|GroupItem {
        try {
            $groupedMenu = static::$factory->get(static::$name);
            if (! $groupedMenu instanceof GroupedMenu) {
                $groupedMenu = new GroupedMenu();
            }

            if (!$groupedMenu instanceof GroupedMenu) {
                throw new Exception('menu not yet initialized');
            }

            if ($groupName) {
                $groupedMenu = $groupedMenu->get($groupName);
                if (! $groupedMenu instanceof GroupItem) {
                    $groupedMenu = new GroupItem();
                }
            }

            if ($groupedMenu instanceof GroupedMenu && $resolvedOnly && $groupedMenu->isNotEmpty()) {
                $groupedMenu = $groupedMenu->each(function (GroupItem $group) {
                    if ($group->items->isNotEmpty()) {
                        $groupItems = $group->items->filter(fn(MenuItem $it) => $it->resolve());
                        $group->items = $groupItems;
                    }

                    return $group;
                });
            }

            return $groupedMenu;
        } catch (Throwable $e) {
            throw_if(app()->hasDebugModeEnabled(), $e);
            app('log')->error('failed on get menu factory\n', [
                'message' => $e->getMessage(),
                'traces' => $e->getTraceAsString(),
            ]);
        }

        return new GroupedMenu();
    }
}
