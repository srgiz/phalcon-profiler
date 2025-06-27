<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Service;

use stdClass as Event;

class Stopwatch
{
    private float $origin;

    private float $final;

    private int $precision = 4;

    private array $stack = [];

    public function __construct()
    {
        $this->origin = $this->final = $this->now(0);
    }

    public function start(string $name): void
    {
        $event = new Event();
        $event->start = $this->now($this->origin);
        $this->stack['active'][$name][] = $event;
    }

    public function stop(string $name): void
    {
        $event = array_pop($this->stack['active'][$name]);
        $event->stop = $this->now($this->origin);
        $event->duration = round($event->stop - $event->start, $this->precision);
        $event->memory = memory_get_usage(true) / 1024 / 1024;
        $this->stack['completed'][$name][] = $event;
    }

    private function now(float $mTime): float
    {
        return round(microtime(true) * 1000 - $mTime, $this->precision);
    }

    /**
     * @internal
     */
    public function final(bool $update): float
    {
        return !$update ? $this->final : ($this->final = $this->now($this->origin));
    }

    /**
     * @internal
     *
     * @return array<string, Event[]>
     */
    public function events(): array
    {
        return $this->stack['completed'] ?? [];
    }
}
