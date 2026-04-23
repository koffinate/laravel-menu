<?php

declare(strict_types=1);

namespace Kfn\Menu;

use Exception;
use Illuminate\Support\Fluent;
use Throwable;

/**
 * @implements Contracts\GroupedMenu
 */
class Factory implements Contracts\GroupedMenu
{
    /** @var string */
    private string $name;

    /** @var Fluent|null */
    private static Fluent|null $factory = null;

    /**
     * @param  string|null  $name
     */
    public function __construct(
        string|null $name = null,
    ) {
        $this->use($name ?: 'main');
        if (! static::$factory instanceof Fluent) {
            static::$factory = new Fluent;
        }
    }

    /**
     * Initialize Factory.
     *
     * @return void
     */
    // public function __invoke(): void
    // {
    //     if (! static::$factory instanceof Fluent) {
    //         static::$factory = new Fluent;
    //     }
    // }

    /**
     * @param  string  $name
     *
     * @return $this
     */
    public function use(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return GroupedMenu
     */
    private function currentGroup(): GroupedMenu
    {
        if (
            ! static::$factory->has($this->name) ||
            ! static::$factory->get($this->name) instanceof GroupedMenu
        ) {
            static::$factory->offsetSet($this->name, new GroupedMenu);
        }

        return static::$factory->{$this->name};
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
     * @throws Exception
     */
    public function add(
        string $name,
        string $title,
        array|object $attributes = [],
        int $sort = 0,
    ): static {
        if (! $this->currentGroup()->has($name)) {
            $this->currentGroup()->add([
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
     * @return GroupedMenu|GroupItem
     * @throws Throwable
     */
    public function get(
        string|null $groupName = null,
        bool $resolvedOnly = true,
    ): GroupedMenu|GroupItem {
        try {
            $groupedMenu = static::$factory->get($this->name);
            if (! $groupedMenu instanceof GroupedMenu) {
                $groupedMenu = new GroupedMenu;
            }

            if (! $groupedMenu instanceof GroupedMenu) {
                throw new Exception('menu not yet initialized');
            }

            if ($groupName) {
                $groupedMenu = $groupedMenu->get($groupName);
                if (! $groupedMenu instanceof GroupItem) {
                    $groupedMenu = new GroupItem;
                }
            }

            if ($groupedMenu instanceof GroupedMenu && $resolvedOnly && $groupedMenu->isNotEmpty()) {
                $groupedMenu = $groupedMenu->each(function (GroupItem $group) {
                    if ($group->items->isNotEmpty()) {
                        $group->items = $group->items->filter(fn (MenuItem $it) => $it->resolve());
                    }

                    return $group;
                });
            }

            return $groupedMenu;
        }
        catch (Throwable $e) {
            throw_if(app()->hasDebugModeEnabled(), $e);
            app('log')->error('failed on get menu factory\n', [
                'message' => $e->getMessage(),
                'traces' => $e->getTraceAsString(),
            ]);
        }

        return new GroupedMenu;
    }
}
