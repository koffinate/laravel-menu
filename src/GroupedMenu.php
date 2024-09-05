<?php

namespace Kfn\Menu;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class GroupedMenu extends Collection
{
    /** @var string */
    private static string $collectionName;

    /**
     * @param $items
     */
    public function __construct($items = [])
    {
        parent::__construct([]);
    }

    /**
     * @param $item
     *
     * @return $this
     * @throws \Exception
     */
    public function add($item): static
    {
        $item = $this->_setItem($item);

        return $this->put($item->get('name'), $item->toArray());
    }

    /**
     * @param ...$values
     *
     * @return $this
     * @throws \Exception
     */
    public function push(...$values): static
    {
        foreach ($values as $value) {
            $this->add($value);
        }

        return $this;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return void
     * @throws \Exception
     */
    public function offsetSet($key, $value): void
    {
        $value = $this->_setItem($value);

        parent::offsetSet($key, new GroupItem(
            name: $value->get('name'),
            title: $value->get('title'),
            attribute: $value->get('attributes'),
            sort: $value->get('sort'),
        ));
    }

    /**
     * @param  mixed  $item
     *
     * @return \Illuminate\Support\Fluent
     * @throws \Exception
     */
    private function _setItem(mixed $item): Fluent
    {
        if (! (is_string($item) || is_array($item) || is_object($item))) {
            throw new Exception('an item must be a string or an array');
        }

        if (is_string($item)) {
            $name = str($item)->slug()->toString();
            $title = $item;
            $attributes = [];
            $sort = 0;
        } else {
            if (is_array($item) || is_object($item)) {
                $item = new Fluent($item);
            }
            $name = $item->get('name')
                ?: str($item->get('title'))->slug()->toString();
            $title = $item->get('title') ?: $name;
            $attributes = $item->get('attributes') ?: [];
            $sort = $item->get('sort') ?: 0;
        }

        return new Fluent([
            'name' => $name,
            'title' => $title,
            'attributes' => $attributes,
            'sort' => $sort,
        ]);
    }

    // public static function init(
    //     string $name = 'default',
    //     string $title = 'Default',
    //     array $attributes = []
    // ): static {
    //     return new static($name, $title, $attributes);
    // }
}
