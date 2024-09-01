<?php

namespace Kfn\Menu;

use Illuminate\Support\Collection;

class GroupedMenu extends Collection
{
    /** @var string */
    private static string $collectionName;

    public function __construct(
        string $name = 'default',
        string $title = 'Default',
        array|object $attributes = [],
        int $sort = 0
    ) {
        if (empty($name)) {
            $name = str($title)->slug()->toString();
        }

        parent::__construct([
            $name => new GroupItem(
                name: $name,
                title: $title,
                attribute: $attributes,
                sort: $sort
            ),
        ]);
    }

    public static function init(
        string $name = 'default',
        string $title = 'Default',
        array $attributes = []
    ): static {
        return new static($name, $title, $attributes);
    }
}
