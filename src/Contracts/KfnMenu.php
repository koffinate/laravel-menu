<?php

namespace Kfn\Menu\Contracts;

use Kfn\Menu\Factory;

interface KfnMenu
{
    /**
     * @param  Factory  $menus
     *
     * @return void
     */
    public function register(Factory $menus): void;
}
