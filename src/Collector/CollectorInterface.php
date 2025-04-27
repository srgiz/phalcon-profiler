<?php

namespace Srgiz\Phalcon\WebProfiler\Collector;

interface CollectorInterface
{
    public function templatePath(): string;

    public function name(): string;

    public function icon(): string;

    /**
     * @return array<string, mixed>
     */
    public function collect(): array;
}
