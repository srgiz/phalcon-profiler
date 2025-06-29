<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler;

use Phalcon\Config\ConfigInterface;
use Phalcon\Di\Di;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Events\Manager;

class WebProfiler implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        /**
         * @var Di $di
         * @var ConfigInterface $profilerConfig
         */
        $profilerDir = dirname(__DIR__);
        $profilerConfig = require_once $profilerDir.'/config/config.php';
        $appConfig = $di->getShared('config')['profiler'] ?? [];
        $collectors = [];

        foreach ($appConfig as $key => $value) {
            if (isset($profilerConfig[$key])) {
                switch ($key) {
                    case 'collectors':
                        /** @var array<Collector\CollectorInterface> $collectors */
                        $collectors = $value->toArray();
                        break;

                    default:
                        $profilerConfig[$key] = $value;
                }
            }
        }

        $profilerConfig['collectors'] = array_merge([
            Collector\RequestCollector::class,
            Collector\PerformanceCollector::class,
            Collector\LogsCollector::class,
            Collector\ExceptionCollector::class,
            Collector\DatabaseCollector::class,
            Collector\RoutingCollector::class,
            Collector\VoltCollector::class,
        ], $collectors);

        $di->setShared('profilerConfig', function () use ($profilerConfig) {
            return $profilerConfig;
        });

        $di->loadFromPhp($profilerDir.'/config/services.php');

        $di->setShared('profilerVolt', [ // need $profilerConfig
            'className' => 'Srgiz\Phalcon\WebProfiler\View\Volt',
            'arguments' => [
                ['type' => 'service', 'name' => 'profilerView'],
            ],
            'calls' => [
                [
                    'method' => 'setOptions',
                    'arguments' => [
                        ['type' => 'parameter', 'value' => [
                            'path' => $profilerConfig->path('viewsCachePath'),
                            'autoescape' => true,
                        ]],
                    ],
                ],
                [
                    'method' => 'setCompiler',
                    'arguments' => [
                        ['type' => 'service', 'name' => 'profilerVoltCompiler'],
                    ],
                ],
            ],
        ]);

        if (!$di->getInternalEventsManager()) {
            $di->setInternalEventsManager(new Manager());
        }

        (new Provider\RouterProvider($profilerConfig['routePrefix']))->register($di);
        (new Provider\EventsProvider())->setExcludeRoutes($profilerConfig['excludeRoutes']->toArray())->register($di);
    }
}
