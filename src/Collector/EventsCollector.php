<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Collector;

use Phalcon\Events\ManagerInterface;

class EventsCollector implements CollectorInterface
{
    public function __construct(private ManagerInterface $eventsManager)
    {
    }

    public function name(): string
    {
        return 'Events';
    }

    public function templatePath(): string
    {
        return '@profiler/profiler/events';
    }

    public function menuPath(): string
    {
        return '@profiler/profiler/events.menu';
    }

    public function collect(): array
    {
        try {
            $reflection = new \ReflectionClass($this->eventsManager);
            $property = $reflection->getProperty('events');

            if (PHP_VERSION_ID < 80100) {
                $property->setAccessible(true);
            }

            /** @var array<string, \SplPriorityQueue> $events */
            $events = $property->getValue($this->eventsManager);

            /** @psalm-suppress UndefinedInterfaceMethod */
            $arePrioritiesEnabled = $this->eventsManager->arePrioritiesEnabled();
        } catch (\Throwable $e) {
            return [];
        }

        $listeners = [];

        foreach ($events as $eventName => $eventQueue) {
            $queue = clone $eventQueue;
            $queue->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
            $queue->top();

            while ($queue->valid()) {
                $current = $queue->current();
                $source = is_object($current['data']) ? get_class($current['data']) : 'callable';
                $method = null;

                if (is_array($current['data'])) {
                    try {
                        $source = is_object($current['data'][0]) ? get_class($current['data'][0]) : $current['data'][0];
                        $method = $current['data'][1];
                    } catch (\Throwable $e) {
                    }
                }

                $listeners[$eventName][] = [
                    'priority' => $current['priority'],
                    'source' => $source,
                    'method' => $method,
                ];

                $queue->next();
            }
        }

        uksort($listeners, static function (string $k1, string $k2) {
            $p1 = explode(':', $k1);
            $p2 = explode(':', $k2);

            $sort = strnatcmp($p1[0], $p2[0]);

            if (0 !== $sort) {
                return $sort;
            }

            return strnatcmp($p2[1] ?? '', $p1[1] ?? '');
        });

        return [
            'listeners' => $listeners,
            'meta' => ['arePrioritiesEnabled' => $arePrioritiesEnabled],
        ];
    }
}
