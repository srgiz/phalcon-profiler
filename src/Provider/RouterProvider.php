<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Provider;

use Phalcon\Di\Di;
use Phalcon\Di\DiInterface;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Dispatcher\DispatcherInterface;
use Phalcon\Events\EventInterface;
use Phalcon\Events\Manager;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\RouterInterface;
use Srgiz\Phalcon\WebProfiler\Controller\ProfilerController;
use Srgiz\Phalcon\WebProfiler\Route;

class RouterProvider implements ServiceProviderInterface
{
    private bool $isResolvedRouter = false;
    private bool $isResolvedEvents = false;

    private array $routes = [
        '_profiler' => [
            'controller' => ProfilerController::class,
            'action' => 'indexAction',
            'pattern' => '',
        ],
        '_profiler-tag' => [
            'controller' => ProfilerController::class,
            'action' => 'tagAction',
            'pattern' => '/tag/{tag}',
        ],
        '_profiler-bar' => [
            'controller' => ProfilerController::class,
            'action' => 'barAction',
            'pattern' => '/bar/{tag}',
        ],
        '_profiler-phpinfo' => [
            'controller' => ProfilerController::class,
            'action' => 'phpinfoAction',
            'pattern' => '/phpinfo',
        ],
    ];

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
        $this->afterResolveEvents($event, $di, $data);
        $this->afterResolveRouter($event, $di, $data);
    }

    public function afterResolveEvents(EventInterface $event, DiInterface $di, array $data): void
    {
        if ($this->isResolvedEvents || 'eventsManager' !== $data['name']) {
            return;
        }

        $this->isResolvedEvents = true;

        /** @var Manager $eventsManager */
        $eventsManager = $data['instance'];
        $eventsManager->attach('micro:beforeHandleRoute', $this, 2048);
    }

    public function beforeHandleRoute(EventInterface $event, Micro $app): bool
    {
        if ($app->getRouter()->getRouteByName('_profiler')) {
            return true;
        }

        $collection = new Micro\Collection();
        $collection->setHandler($app->getSharedService(ProfilerController::class));

        foreach ($this->routes as $name => $route) {
            $collection->get($this->routePrefix.$route['pattern'], $route['action'], $name);
        }

        $app->mount($collection);

        return true;
    }

    public function afterResolveRouter(EventInterface $event, DiInterface $di, array $data): void
    {
        if ($this->isResolvedRouter || 'router' !== $data['name']) {
            return;
        }

        $this->isResolvedRouter = true;

        /** @var RouterInterface $router */
        $router = $data['instance'];

        foreach ($this->routes as $name => $route) {
            $router->attach(
                (new Route($this->routePrefix.$route['pattern'], [
                    'controller' => $route['controller'],
                    'action' => $route['action'],
                ], 'GET'))->beforeMatch($this->beforeMatchRoute())->setName($name)
            );
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
