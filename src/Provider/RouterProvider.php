<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Provider;

use Phalcon\Di\Di;
use Phalcon\Di\DiInterface;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Dispatcher\DispatcherInterface;
use Phalcon\Events\EventInterface;
use Phalcon\Mvc\RouterInterface;
use Srgiz\Phalcon\WebProfiler\Controller\ProfilerController;
use Srgiz\Phalcon\WebProfiler\Route;

class RouterProvider implements ServiceProviderInterface
{
    private bool $isResolved = false;

    public function __construct(private string $routePrefix)
    {
    }

    public function register(DiInterface $di): void
    {
        /** @var Di $di */
        $di->getInternalEventsManager()->attach('di:afterServiceResolve', $this);
    }

    public function afterServiceResolve(EventInterface $event, DiInterface $di, array $data): void
    {
        if ($this->isResolved || 'router' !== $data['name']) {
            return;
        }

        $this->isResolved = true;

        /** @var RouterInterface $router */
        $router = $data['instance'];

        $routes = [
            (new Route($this->routePrefix, [
                'controller' => ProfilerController::class,
                'action' => 'indexAction',
            ], 'GET'))->beforeMatch($this->beforeMatchRoute())->setName('_profiler'),
            (new Route($this->routePrefix.'/tag/{tag}', [
                'controller' => ProfilerController::class,
                'action' => 'tagAction',
            ], 'GET'))->beforeMatch($this->beforeMatchRoute())->setName('_profiler-tag'),
            (new Route($this->routePrefix.'/bar/{tag}', [
                'controller' => ProfilerController::class,
                'action' => 'barAction',
            ], 'GET'))->beforeMatch($this->beforeMatchRoute())->setName('_profiler-bar'),
        ];

        foreach ($routes as $route) {
            $router->attach($route);
        }
    }

    private function beforeMatchRoute(): callable
    {
        return function (string $uri, Route $route, InjectionAwareInterface $router) {
            /** @var DispatcherInterface $dispatcher */
            $dispatcher = $router->getDI()->getShared('dispatcher');
            $paths = $route->getPaths();

            // https://github.com/phalcon/cphalcon/issues/16238#issuecomment-1613262031
            if (preg_match(sprintf('/(.+)\\\\((.+)%s)$/', $dispatcher->getHandlerSuffix()), $paths['controller'], $matches)) {
                // 'ProfilerController' => 'Profiler'
                $route->setProfilerController($matches[1], $matches[3]);
            }

            // 'indexAction' => 'index'
            $route->setProfilerAction(str_replace($dispatcher->getActionSuffix(), '', (string) $paths['action']));

            return true;
        };
    }
}
