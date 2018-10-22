<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 07/10/18
 * Time: 00:36
 */

namespace Framework\Router;

/**
 * Class Route
 * Represent a matched route
 * @package Framework\Router
 */
class Route
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @var array
     */
    private $params;

    /**
     * Route constructor.
     * @param string $name
     * @param string|callable $callback
     * @param array $params
     */
    public function __construct(string $name, $callback, array $params)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->params = $params;
    }

    /**
     * retrieve the name of the URL
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Retrieve the callback
     * @return string|callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Retrieve the URL parameters
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
