<?php

namespace Devorto\Routing;

/**
 * Interface Controller
 *
 * @package Devorto\Routing
 */
interface Controller
{
    /**
     * @param string $route
     */
    public function handleRoute(string $route): void;
}
