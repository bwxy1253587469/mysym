<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing;

use Symfony\Component\Config\Resource\ResourceInterface;

/**
 * A RouteCollection represents a set of Route instances.
 *
 * When adding a route at the end of the collection, an existing route
 * with the same name is removed first. So there can only be one route
 * with a given name.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 */
class RouteCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var Route[]
     */
    private $routes = array();

    /**
     * @var array
     */
    private $resources = array();

    public function __clone()
    {
        foreach ($this->routes as $name => $route) {
            $this->routes[$name] = clone $route;
        }
    }

    /**
     * Gets the current RouteCollection as an Iterator that includes all routes.
     *
     * It implements \IteratorAggregate.
     *
     * @see all()
     *
     * @return \ArrayIterator|Route[] An \ArrayIterator object for iterating over routes
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }

    /**
     * Gets the number of Routes in this collection.
     *
     * @return int The number of routes
     */
    public function count()
    {
        return count($this->routes);
    }

    /**
     * Adds a route.
     *
     * @param string $name  The route name
     * @param Route  $route A Route instance
     */
    public function add($name, Route $route)
    {
        unset($this->routes[$name]);

        $this->routes[$name] = $route;
    }
}
