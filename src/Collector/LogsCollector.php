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

    public function name(): string
    {
        return 'Logs';
    }

    public function icon(): string
    {
        return <<<HTML

<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-square" viewBox="0 0 16 16">
    <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
    <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
</svg>
HTML;
    }

    public function collect(): array
    {
        $items = [];
        $buttons = [];

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

            $buttons[$item->getLevel()]['count'] += 1;
        }

        ksort($buttons);

        return [
            'items' => $items,
            'buttons' => $buttons,
        ];
    }
}
