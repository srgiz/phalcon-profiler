<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Collector;

use Phalcon\Dispatcher\DispatcherInterface;
use Phalcon\Events\EventInterface;

class ExceptionCollector implements CollectorInterface
{
    private ?\Throwable $exception = null;

    public function beforeException(EventInterface $event, DispatcherInterface $dispatcher, \Throwable $exception): bool
    {
        $this->exception = $exception;

        return true;
    }

    public function templatePath(): string
    {
        return '@profiler/profiler/exception';
    }

    public function menuPath(): string
    {
        return '@profiler/profiler/exception.menu';
    }

    public function name(): string
    {
        return 'Exception';
    }

    public function collect(): array
    {
        $trace = [];

        foreach ($this->exception?->getTrace() ?? [] as $key => $item) {
            unset($item['args']);
            $trace[$key] = $item;
        }

        return [
            'class' => $this->exception ? get_class($this->exception) : null,
            'message' => $this->exception?->getMessage(),
            'code' => $this->exception?->getCode(),
            'file' => $this->exception?->getFile(),
            'line' => $this->exception?->getLine(),
            'trace' => $trace,
            'meta' => ['count' => $this->exception ? 1 : 0],
        ];
    }
}
