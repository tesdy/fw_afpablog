<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 07/10/18
 * Time: 00:35
 */

namespace Framework;

use Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;

/**
 * Register and match route
 * Class Router
 * @package Framework
 */
class Router
{

    /**
     * @var FastRouteRouter
     */
    private $fastRouter;

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $this->fastRouter = new FastRouteRouter();
    }

    /**
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     */
    public function get(string $path, $callable, ?string $name = null): void
    {
        $this->fastRouter->addRoute(new ZendRoute($path, $callable, ['GET'], $name));
    }

    /**
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     */
    public function post(string $path, $callable, ?string $name = null): void
    {
        $this->fastRouter->addRoute(new ZendRoute($path, $callable, ['POST'], $name));
    }

    /**
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     */
    public function delete(string $path, $callable, ?string $name = null): void
    {
        $this->fastRouter->addRoute(new ZendRoute($path, $callable, ['DELETE'], $name));
    }

    /**
     * Génère les routes du CRUD
     * @param string $prefixPath
     * @param $callback
     * @param null|string $prefixName
     */
    public function crud(string $prefixPath, $callback, ?string $prefixName)
    {
        $this->get("$prefixPath", $callback, $prefixName. '.index');
        $this->get("$prefixPath/new", $callback, "$prefixName.create");
        $this->post("$prefixPath/new", $callback);
        $this->get("$prefixPath/{id:\d+}", $callback, "$prefixName.edit");
        $this->post("$prefixPath/{id:\d+}", $callback);
        $this->delete("$prefixPath/{id:\d+}", $callback, "$prefixName.delete");
    }

    /**
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->fastRouter->match($request);
        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),
                $result->getMatchedMiddleware(),
                $result->getMatchedParams()
            );
        }
        return null;
    }

    // fonction à créer après avoir crée {ROUTERTEST} -> public function testGenerateUri(): void
    /**
     * @param string $pathname
     * @param array $params
     * @param array $queryParams
     * @return null|string
     */
    public function generateUri(string $pathname, array $params = [], array $queryParams = []): ?string
    {
        $uri = $this->fastRouter->generateUri($pathname, $params);
        if (!empty($queryParams)) {
            return $uri . '?' . http_build_query($queryParams);
        }
        return $uri;
    }
}
