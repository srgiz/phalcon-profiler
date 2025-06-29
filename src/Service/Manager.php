<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Service;

use Phalcon\Config\ConfigInterface;
use Phalcon\Di\AbstractInjectionAware;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Http\RequestInterface;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Router\Exception as RouterException;
use Phalcon\Mvc\Router\RouteInterface;
use Srgiz\Phalcon\WebProfiler\Collector\CollectorInterface;

class Manager extends AbstractInjectionAware
{
    /**
     * @return array<CollectorInterface>
     */
    public function collectors(): array
    {
        static $collectors = [];

        if ($collectors) {
            return $collectors;
        }

        foreach ($this->config()['collectors'] as $name) {
            /** @var CollectorInterface $collector */
            $collector = $this->getDI()->getShared($name);
            $collectors[$collector->name()] = $collector;
        }

        return $collectors;
    }

    private function config(): ConfigInterface
    {
        return $this->getDI()->getShared('profilerConfig');
    }

    public function bar(string $tag): array
    {
        return [
            '_meta' => (new DataReader($this->config()['tagsDir'].'/'.$tag))->read('_meta'),
            '_tag' => $tag,
        ];
    }

    public function requests(): array
    {
        $dir = $this->config()['tagsDir'];
        $files = scandir($dir, SCANDIR_SORT_DESCENDING);
        $data = [];

        foreach ($files as $tag) {
            if (in_array($tag, ['.', '..', '.gitignore'])) {
                continue;
            }

            try {
                $data[$tag] = (new DataReader($dir.'/'.$tag))->read('_meta');
            } catch (RouterException $e) {
            }
        }

        return $data;
    }

    /**
     * @throws RouterException
     */
    public function data(string $tag, string $panel): array
    {
        $collectors = $this->collectors();
        $collector = $collectors[$panel] ?? current($collectors);
        $panel = $collector->name();
        $dir = $this->config()['tagsDir'];

        if ('latest' === $tag) {
            $files = glob($dir.'/*');
            sort($files);
            $file = array_pop($files);

            if (is_string($file)) {
                $tag = basename($file);
            }
        }

        $archive = new DataReader($dir.'/'.$tag);

        return array_merge($archive->read($panel), [
            '_meta' => $archive->read('_meta'),
            '_templatePath' => $collector->templatePath(),
            '_tag' => $tag,
            '_panel' => $panel,
        ]);
    }

    public function save(string $tag, \DateTimeInterface $requestTime, InjectionAwareInterface $app, ResponseInterface $response): void
    {
        $dir = $this->config()['tagsDir'];

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $archive = new DataWriter($dir.'/'.$tag);
        $metaCollectors = [];

        foreach ($this->collectors() as $collector) {
            $archive->add($collector->name(), $data = $collector->collect());

            if (isset($data['meta'])) {
                $metaCollectors[$collector->name()] = $data['meta'];
            }
        }

        /**
         * @var RequestInterface $request
         * @var RouteInterface|null $route
         * @var Stopwatch $stopwatch
         */
        $request = $app->getDI()->getShared('request');
        $route = $app->getDI()->getShared('router')->getMatchedRoute();
        $stopwatch = $app->getDI()->getShared('profilerStopwatch');

        $archive->add('_meta', [
            'method' => $request->getMethod(),
            'uri' => $request->getURI(),
            'statusCode' => $response->getStatusCode() ?? 200,
            'requestTime' => $requestTime,
            'executionTime' => $stopwatch->final(false),
            'peakMemoryUsage' => memory_get_peak_usage(true) / 1024 / 1024,
            'route' => !$route ? null : sprintf('%s [%s]', $route->getName(), $route->getRouteId()),
            'collectors' => $metaCollectors,
        ]);
    }
}
