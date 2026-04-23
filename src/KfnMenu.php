<?php

namespace Kfn\Menu;

abstract class KfnMenu implements Contracts\KfnMenu
{
    /** @var string|null */
    protected string|null $name = null;

    /** @var Factory */
    protected Factory $manus;

    public function __construct(string|null $name = null)
    {
        $this->name ??= $name ?? 'main';
        $this->manus = new Factory($this->name);
        $this->register($this->manus);
    }

    abstract public function register(Factory $menus): void;
}
