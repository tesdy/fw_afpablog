<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 09/10/18
 * Time: 22:39
 */

namespace Framework\Router;

use Framework\Router;

/**
 * Class RouterTwigExtension
 * @package Framework\Router
 */
class RouterTwigExtension extends \Twig_Extension
{

    /**
     * @var Router
     */
    private $router;

    /**
     * RouterTwigExtension constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('path', [$this, 'pathFor']),
            new \Twig_SimpleFunction('is_subpath', [$this, 'isSubPath'])
        ];
    }

    /**
     * @param string $path
     * @param array $params
     * @return string
     */
    public function pathFor(string $path, array $params = []): string
    {
        return $this->router->generateUri($path, $params);
    }

    public function isSubPath(string $path): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $expectedUri = $this->router->generateUri($path);
        return strpos($uri, $expectedUri) !== false;
    }
}
