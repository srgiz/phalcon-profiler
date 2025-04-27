<?php
declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler;

use Phalcon\Config\ConfigInterface;
use Phalcon\Di\Di;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Events\Manager;
use Srgiz\Phalcon\WebProfiler\Collector;
use Srgiz\Phalcon\WebProfiler\Provider;

class WebProfiler implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        /**
         * @var Di $di
         * @var ConfigInterface $profilerConfig
         */
        $profilerDir = dirname(__DIR__);
        $profilerConfig = require_once $profilerDir . '/config/config.php';
        $appConfig = $di->getShared('config')['profiler'] ?? [];
        $collectors = [];

        foreach ($appConfig as $key => $value) {
            if (isset($profilerConfig[$key])) {
                if ('collectors' === $key) {
                    /** @var array<Collector\CollectorInterface> $collectors */
                    $collectors = $value->toArray();
                    continue;
                }

                $profilerConfig[$key] = $value;
            }
        }

        $profilerConfig['collectors'] = array_merge([
            Collector\RequestCollector::class,
            Collector\PerformanceCollector::class,
            Collector\DbCollector::class,
            Collector\LogsCollector::class,
            Collector\ExceptionCollector::class,
            Collector\ViewCollector::class,
        ], $collectors);

        $di->setShared('profilerConfig', function () use ($profilerConfig) {
            return $profilerConfig;
        });

        $di->loadFromYaml($profilerDir . '/config/services.yaml', [
            '!profilerConfig' => function (string $path) use ($profilerConfig) {
                return $profilerConfig->path($path);
            },
        ]);

        if (!$di->getInternalEventsManager()) {
            $di->setInternalEventsManager(new Manager());
        }

        (new Provider\RouterProvider($profilerConfig['routePrefix']))->register($di);
        (new Provider\EventsProvider())->register($di);
    }
}
