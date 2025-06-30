<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Collector;

use Phalcon\Events\EventInterface;
use Phalcon\Logger\Adapter\AdapterInterface;
use Phalcon\Logger\Item;

class LogsCollector implements CollectorInterface
{
    private array $logs = [];

    public function log(EventInterface $event, AdapterInterface $adapter, Item $item): void
    {
        $this->logs[] = [
            'item' => $item,
            'message' => $adapter->getFormatter()->format($item),
            'backtrace' => array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 5),
        ];
    }

    public function templatePath(): string
    {
        return '@profiler/profiler/logs';
    }

    public function menuPath(): string
    {
        return '@profiler/profiler/logs.menu';
    }

    public function name(): string
    {
        return 'Logs';
    }

    public function collect(): array
    {
        $items = [];
        $buttons = [];
        $metaCount = 0;

        foreach ($this->logs as $log) {
            /** @var Item $item */
            $item = $log['item'];

            $items[] = [
                'level' => $item->getLevel(),
                'levelName' => $item->getLevelName(),
                'datetime' => $item->getDateTime(),
                'context' => $item->getContext(),
                'message' => $log['message'],
                'backtrace' => $log['backtrace'],
            ];

            if (!isset($buttons[$item->getLevel()])) {
                $buttons[$item->getLevel()] = [
                    'name' => $item->getLevelName(),
                    'count' => 0,
                ];
            }

            if ($item->getLevel() <= 4) {
                ++$metaCount;
            }

            ++$buttons[$item->getLevel()]['count'];
        }

        ksort($buttons);

        return [
            'items' => $items,
            'buttons' => $buttons,
            'meta' => ['count' => $metaCount],
        ];
    }
}
