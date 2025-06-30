<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Collector;

use Phalcon\Mvc\RouterInterface;

class RoutingCollector implements CollectorInterface
{
    public function __construct(private RouterInterface $router)
    {
    }

    public function name(): string
    {
        return 'Routing';
    }

    public function templatePath(): string
    {
        return '@profiler/profiler/routing';
    }

    public function menuPath(): string
    {
        return '@profiler/profiler/routing.menu';
    }

    public function collect(): array
    {
        $routes = [];
        $match = $this->router->getMatchedRoute();

        foreach ($this->router->getRoutes() as $route) {
            $key = $route->getRouteId();

            if ($match && $route->getRouteId() === $match->getRouteId()) {
                $key = -1;
            }

            $routes[$key] = [
                'id' => $route->getRouteId(),
                'name' => $route->getName(),
                'pattern' => $route->getPattern(),
            ];
        }

        ksort($routes);

        return [
            'routes' => array_values($routes),
            'match' => null !== $match,
        ];
    }
}
