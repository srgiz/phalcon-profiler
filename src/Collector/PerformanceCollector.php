<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Collector;

use Phalcon\Db\Adapter\AdapterInterface;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Dispatcher\DispatcherInterface;
use Phalcon\Events\EventInterface;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\View\Engine\EngineInterface;
use Srgiz\Phalcon\WebProfiler\Service\Stopwatch;

class PerformanceCollector implements CollectorInterface
{
    private float $maxScale = 0;

    public function __construct(private Stopwatch $stopwatch)
    {
    }

    public function templatePath(): string
    {
        return '@profiler/profiler/performance';
    }

    public function menuPath(): string
    {
        return '@profiler/profiler/performance.menu';
    }

    public function name(): string
    {
        return 'Performance';
    }

    public function collect(): array
    {
        $data = ['labels' => ['']];

        foreach ($this->stopwatch->events() as $name => $events) {
            $dataset = ['labelShort' => $name];
            $sumDuration = 0;
            $maxMemory = 0;

            foreach ($events as $event) {
                $dataset['data'][] = ['y' => '', 'x' => [$event->start, $event->stop], 'duration' => $event->duration, 'memory' => sprintf('%.2F', $event->memory)];
                $sumDuration += $event->duration;
                $maxMemory = max($maxMemory, $event->memory);
            }

            $dataset['label'] = $name.': '.$sumDuration.' ms / '.sprintf('%.2F MiB', $maxMemory);
            $data['datasets'][] = $dataset;
        }

        return [
            'data' => $data,
            'meta' => ['max' => $this->maxScale],
        ];
    }

    public function beforeSendResponse(EventInterface $event, InjectionAwareInterface $app, ResponseInterface $response): void
    {
        $this->maxScale = $this->stopwatch->final(true);
    }

    public function boot(EventInterface $event, InjectionAwareInterface $app): bool
    {
        $this->stopwatch->start('request'); // before router

        return true;
    }

    public function beforeHandleRequest(EventInterface $event, InjectionAwareInterface $app): bool
    {
        $this->stopwatch->stop('request'); // after router

        return true;
    }

    public function beforeDispatch(EventInterface $event, DispatcherInterface $dispatcher): bool
    {
        $this->stopwatch->start('request'); // before dispatch

        return true;
    }

    public function afterBinding(EventInterface $event, DispatcherInterface $dispatcher): bool
    {
        $this->stopwatch->stop('request'); // after dispatch

        return true;
    }

    public function beforeExecuteRoute(EventInterface $event, DispatcherInterface $dispatcher): bool
    {
        $this->stopwatch->start('controller');

        return true;
    }

    public function afterExecuteRoute(EventInterface $event, DispatcherInterface $dispatcher): bool
    {
        $this->stopwatch->stop('controller');

        return true;
    }

    public function beforeQuery(EventInterface $event, AdapterInterface $conn): bool
    {
        $this->stopwatch->start('db');

        return true;
    }

    public function afterQuery(EventInterface $event, AdapterInterface $conn): void
    {
        $this->stopwatch->stop('db');
    }

    public function beforeCompile(EventInterface $event, EngineInterface $engine): bool
    {
        $this->stopwatch->start('view');

        return true;
    }

    public function afterCompile(EventInterface $event, EngineInterface $engine): bool
    {
        $this->stopwatch->stop('view');

        return true;
    }
}
