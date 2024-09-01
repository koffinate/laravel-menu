<?php

namespace Kfn\Menu\Contracts;

interface MenuItem
{
    /**
     * @return string
     */
    public function getHref(): string;

    /**
     * @return bool
     */
    public function resolve(): bool;

    /**
     * @return bool
     */
    public function isActive(): bool;
}
