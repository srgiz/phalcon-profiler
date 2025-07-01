<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Provider;

use Phalcon\Di\Di;
use Phalcon\Di\DiInterface;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Events\EventInterface;
use Phalcon\Events\Manager;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\RouterInterface;
use Phalcon\Mvc\ViewBaseInterface;
use Srgiz\Phalcon\WebProfiler\Collector;
use Srgiz\Phalcon\WebProfiler\Service\Manager as Profiler;

class EventsProvider implements ServiceProviderInterface
{
    private \DateTimeInterface $requestTime;

    private string $profilerTag;

    private bool $isResolved = false;

    private array $excludeRoutes = [];

    public function __construct()
    {
        $this->requestTime = new \DateTimeImmutable();
        $this->profilerTag = uniqid();
    }

    public function register(DiInterface $di): void
    {
        /** @var Di $di */
        $di->getInternalEventsManager()->attach('di:afterServiceResolve', $this);
    }

    public function setExcludeRoutes(array $routes): self
    {
        $this->excludeRoutes = $routes;

        return $this;
    }

    public function afterServiceResolve(EventInterface $event, DiInterface $di, array $data): void
    {
        if ($this->isResolved || 'eventsManager' !== $data['name']) {
            return;
        }

        $this->isResolved = true;

        /** @var Manager $eventsManager */
        $eventsManager = $data['instance'];

        $events = [
            // profiler
            ['view:beforeRender', $this, 1024],
            ['application:beforeSendResponse', $this, -1024],
            // request
            ['application:beforeSendResponse',  Collector\RequestCollector::class, 1024],
            // performance
            ['application:beforeSendResponse', Collector\PerformanceCollector::class, 2048],
            ['application:boot', Collector\PerformanceCollector::class, 1024],
            ['application:beforeHandleRequest', Collector\PerformanceCollector::class, 1024],
            ['dispatch:beforeDispatch', Collector\PerformanceCollector::class, 1024],
            ['dispatch:afterBinding', Collector\PerformanceCollector::class, 1024],
            ['dispatch:beforeExecuteRoute', Collector\PerformanceCollector::class, 1024],
            ['dispatch:afterExecuteRoute', Collector\PerformanceCollector::class, 1024],
            ['db:beforeQuery', Collector\PerformanceCollector::class, 2048],
            ['db:afterQuery', Collector\PerformanceCollector::class, 2048],
            ['view:beforeCompile', Collector\PerformanceCollector::class, 1024],
            ['view:afterCompile', Collector\PerformanceCollector::class, 2048],
            // db
            ['db:beforeQuery', Collector\DatabaseCollector::class, 1024],
            ['db:afterQuery', Collector\DatabaseCollector::class, 1024],
            ['db:beginTransaction', Collector\DatabaseCollector::class, 1024],
            ['db:commitTransaction', Collector\DatabaseCollector::class, 1024],
            ['db:rollbackTransaction', Collector\DatabaseCollector::class, 1024],
            // logger
            ['profiler:log', Collector\LogsCollector::class],
            // exception
            ['dispatch:beforeException', Collector\ExceptionCollector::class, 1024],
            // view
            ['view:afterCompile', Collector\VoltCollector::class, 1024],
        ];

        foreach ($events as $event) {
            [$name, $obj] = $event;
            $eventsManager->attach($name, is_object($obj) ? $obj : $di->getShared($obj), $event[2] ?? Manager::DEFAULT_PRIORITY);
        }
    }

    public function beforeRender(EventInterface $event, ViewBaseInterface $view): bool
    {
        $view->setVar('_profilerTag', $this->profilerTag);

        return true;
    }

    public function beforeSendResponse(EventInterface $event, InjectionAwareInterface $app, ResponseInterface $response): void
    {
        /** @var RouterInterface $router */
        $router = $app->getDI()->getShared('router');

        if (
            str_starts_with(strval($router->getMatchedRoute()?->getName()), '_profiler')
            || in_array($app->getDI()->getShared('request')->getURI(true), $this->excludeRoutes, true)
        ) {
            return;
        }

        $response->setHeader('X-Profiler-Tag', $this->profilerTag);

        try {
            /** @var Profiler $profiler */
            $profiler = $app->getDI()->getShared('profilerManager');
            $profiler->save($this->profilerTag, $this->requestTime, $app, $response);
        } catch (\Throwable $e) {
            $response->setHeader('X-Profiler-Error', $e->getMessage());
        }
    }
}
