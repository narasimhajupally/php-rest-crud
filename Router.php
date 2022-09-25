<?php

declare(strict_types=1);

final class Router
{
    private const NO_ROUTE = 404;

    /**
     * @var \ArrayIterator<Route>
     */
    private $routes;

    /**
     * Router constructor.
     * @param $routes array<Route>
     */
    public function __construct(array $routes = [])
    {
        $this->routes = new \ArrayIterator();
        foreach ($routes as $route) {
            $this->add($route);
        }
    }

    public function add(Route $route): self
    {
        $this->routes->offsetSet($route->getName(), $route);
        return $this;
    }

    public function matchFromPath(string $path, string $method): Route
    {
        foreach ($this->routes as $route) {
            if ($route->match($path, $method) === false) {
                continue;
            }
            return $route;
        }

        throw new \Exception(
            'No route found for ' . $method,
            self::NO_ROUTE
        );
    }

    public function generateUri(string $name, array $parameters = []): string
    {
        if ($this->routes->offsetExists($name) === false) {
            throw new \InvalidArgumentException(
                sprintf('Unknown %s name route', $name)
            );
        }
        $route = $this->routes[$name];
        if ($route->hasVars() && $parameters === []) {
            throw new \InvalidArgumentException(
                sprintf('%s route need parameters: %s', $name, implode(',', $route->getVarsNames()))
            );
        }
        return self::resolveUri($route, $parameters);
    }

    private static function resolveUri(Route $route, array $parameters): string
    {
        $uri = $route->getPath();
        foreach ($route->getVarsNames() as $variable) {
            $varName = trim($variable, '{\}');
            if (array_key_exists($varName, $parameters) === false) {
                throw new \InvalidArgumentException(
                    sprintf('%s not found in parameters to generate url', $varName)
                );
            }
            $uri = str_replace($variable, strval($parameters[$varName]), $uri);
        }
        return $uri;
    }
}
