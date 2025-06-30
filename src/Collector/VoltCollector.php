<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Collector;

use Phalcon\Events\EventInterface;
use Phalcon\Mvc\View\Engine\AbstractEngine;

class VoltCollector implements CollectorInterface
{
    private array $data = [
        'activeRenderPaths' => [],
    ];

    public function afterCompile(EventInterface $event, AbstractEngine $engine): bool
    {
        if (method_exists($engine->getView(), 'getActiveRenderPath')) {
            $activeRenderPath = $engine->getView()->getActiveRenderPath();

            if (is_array($activeRenderPath)) {
                $activeRenderPath = current($activeRenderPath);
            }

            $this->data['activeRenderPaths'][] = [
                'path' => $activeRenderPath,
                'backtrace' => array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 3),
            ];
        }

        return true;
    }

    public function templatePath(): string
    {
        return '@profiler/profiler/volt';
    }

    public function menuPath(): string
    {
        return '@profiler/profiler/volt.menu';
    }

    public function name(): string
    {
        return 'Volt';
    }

    public function collect(): array
    {
        return $this->data;
    }
}
