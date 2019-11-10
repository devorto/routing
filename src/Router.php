<?php

namespace Devorto\Routing;

use Devorto\KeyValueStorage\KeyValueStorage;
use InvalidArgumentException;

/**
 * Class Router
 *
 * @package Devorto\Routing
 */
class Router
{
    /**
     * @var string
     */
    protected $notFound;

    /**
     * @var string
     */
    protected $home;

    /**
     * Router constructor.
     *
     * @param string $notFound Pass Controller as string and must implement that interface, required.
     * Will be used when a route could not be matched.
     * @param string|null $home Pass Controller as string and must implement that interface, optional.
     * Used when a route is empty, if not provided $notFound will be used instead.
     */
    public function __construct(string $notFound, string $home = null)
    {
        // By forcing a string instead we don't need loaded classes.
        if (!is_subclass_of($notFound, Controller::class, true)) {
            throw new InvalidArgumentException($notFound . ' does not implement ' . Controller::class);
        }

        if (empty($home)) {
            $home = $notFound;
        } else {
            if (!is_subclass_of($home, Controller::class, true)) {
                throw new InvalidArgumentException($home . ' does not implement ' . Controller::class);
            }
        }

        $this->notFound = $notFound;
        $this->home = $home;
    }

    /**
     * Matches a route in KeyValueStorage and returns it's.
     *
     * @param string $route The route you like to find/match in $routes.
     * @param KeyValueStorage $routes A list of routes (key) and their corresponding controller (value).
     *
     * @return string The class including namespace implementing Controller interface, loaded but not instantiated.
     */
    public function match(string $route, KeyValueStorage $routes): string
    {
        $route = strtolower(trim(rtrim($route, '/')));

        // If empty, homepage is expected.
        if (empty($route)) {
            return $this->home;
        }

        foreach ($routes as $matcher => $controller) {
            // It's a route regex:
            if (preg_match('/^\/\^.+\$\/$/', $matcher) === 1) {
                if (preg_match($matcher, $route) === 1 && is_subclass_of($controller, Controller::class, true)) {
                    return $controller;
                }
            } elseif ($matcher === $route && is_subclass_of($controller, Controller::class, true)) {
                return $controller;
            }
        }

        return $this->notFound;
    }
}
