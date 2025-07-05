<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Provider;

use Phalcon\Di\Di;
use Phalcon\Di\DiInterface;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Events\EventInterface;
use Phalcon\Events\Manager;
use Phalcon\Http\Response;
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

    public function __construct(private array $excludeRoutes)
    {
        $this->requestTime = new \DateTimeImmutable();
        $this->profilerTag = uniqid();
    }

    public function register(DiInterface $di): void
    {
        /** @var Di $di */
        $di->getInternalEventsManager()->attach('di:afterServiceResolve', $this);
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
            // request
            ['application:boot', Collector\RequestCollector::class, 1536], // clear
            ['application:beforeSendResponse',  Collector\RequestCollector::class, 1024],
            ['micro:beforeHandleRoute',  Collector\RequestCollector::class, 1280],
            ['micro:afterHandleRoute',  Collector\RequestCollector::class, 1024],
            // performance
            ['application:beforeSendResponse', Collector\PerformanceCollector::class, 2048],
            ['micro:afterHandleRoute', Collector\PerformanceCollector::class, 2048],
            ['micro:beforeException', Collector\PerformanceCollector::class, 2048],
            ['application:boot', Collector\PerformanceCollector::class, 1024],
            ['application:beforeHandleRequest', Collector\PerformanceCollector::class, 1024],
            ['dispatch:beforeDispatch', Collector\PerformanceCollector::class, 1024],
            ['dispatch:afterBinding', Collector\PerformanceCollector::class, 1024],
            ['dispatch:beforeExecuteRoute', Collector\PerformanceCollector::class, 1024],
            ['dispatch:afterExecuteRoute', Collector\PerformanceCollector::class, 1024],
            ['micro:beforeHandleRoute', Collector\PerformanceCollector::class, 1024],
            ['micro:beforeExecuteRoute', [Collector\PerformanceCollector::class, 'microAfterRequest'], 2048],
            ['micro:beforeExecuteRoute', Collector\PerformanceCollector::class, 1024],
            ['micro:afterExecuteRoute', Collector\PerformanceCollector::class, 1024],
            ['micro:beforeNotFound', Collector\PerformanceCollector::class, 1024], // alt microAfterRequest
            ['db:beforeQuery', Collector\PerformanceCollector::class, 1024],
            ['db:afterQuery', Collector\PerformanceCollector::class, 2048],
            ['view:beforeCompile', Collector\PerformanceCollector::class, 1024],
            ['view:afterCompile', Collector\PerformanceCollector::class, 2048],
            // db
            ['application:boot', Collector\DatabaseCollector::class, 1536], // clear
            ['micro:beforeHandleRoute', [Collector\DatabaseCollector::class, 'boot'], 1536], // clear
            ['db:beforeQuery', Collector\DatabaseCollector::class, 2048],
            ['db:afterQuery', Collector\DatabaseCollector::class, 1024],
            ['db:beginTransaction', Collector\DatabaseCollector::class, 1024],
            ['db:commitTransaction', Collector\DatabaseCollector::class, 1024],
            ['db:rollbackTransaction', Collector\DatabaseCollector::class, 1024],
            // logger
            ['application:boot', Collector\LogsCollector::class, 1536], // clear
            ['micro:beforeHandleRoute', [Collector\LogsCollector::class, 'boot'], 1536], // clear
            ['profiler:log', Collector\LogsCollector::class, 1024],
            // exception
            ['application:boot', Collector\ExceptionCollector::class, 1536], // clear
            ['dispatch:beforeException', Collector\ExceptionCollector::class, 1024],
            ['micro:beforeHandleRoute', [Collector\ExceptionCollector::class, 'boot'], 1536], // clear
            ['micro:beforeException', Collector\ExceptionCollector::class, 1024],
            // view
            ['application:boot', Collector\VoltCollector::class, 1536], // clear
            ['micro:beforeHandleRoute', [Collector\VoltCollector::class, 'boot'], 1536], // clear
            ['view:afterCompile', Collector\VoltCollector::class, 1024],
            // profiler
            ['view:beforeRender', $this, 1024],
            ['application:beforeSendResponse', $this, -1024],
            ['micro:afterHandleRoute', $this, -1024],
            ['micro:beforeException', $this, -1024], // alt afterHandleRoute
        ];

        foreach ($events as $event) {
            [$name, $handler, $priority] = $event;

            if (is_string($handler)) {
                $handler = $di->getShared($handler);
            } elseif (is_array($handler)) {
                $handler = [$di->getShared($handler[0]), $handler[1]];
            }

            $eventsManager->attach($name, $handler, $priority);
        }
    }

    public function beforeRender(EventInterface $event, ViewBaseInterface $view): bool
    {
        $view->setVar('_profilerTag', $this->profilerTag);

        return true;
    }

    // app
    public function beforeSendResponse(EventInterface $event, InjectionAwareInterface $app, ?ResponseInterface $response): void
    {
        /** @var RouterInterface $router */
        $router = $app->getDI()->getShared('router');

        if (
            str_starts_with(strval($router->getMatchedRoute()?->getName()), '_profiler')
            || in_array($app->getDI()->getShared('request')->getURI(true), $this->excludeRoutes, true)
        ) {
            return;
        }

        $response?->setHeader('X-Profiler-Tag', $this->profilerTag);

        try {
            /** @var Profiler $profiler */
            $profiler = $app->getDI()->getShared('profilerManager');
            $profiler->save($this->profilerTag, $this->requestTime, $app, $response ?: null);
        } catch (\Throwable $e) {
            $response?->setHeader('X-Profiler-Error', $e->getMessage());
        }
    }

    // micro
    public function afterHandleRoute(EventInterface $event, InjectionAwareInterface $app, mixed $returnedValue): void
    {
        $this->beforeSendResponse($event, $app, $returnedValue instanceof ResponseInterface ? $returnedValue : null);
    }

    // micro
    public function beforeException(EventInterface $event, InjectionAwareInterface $app, \Throwable $e): void
    {
        $this->beforeSendResponse($event, $app, new Response(null, 500));
    }
}
