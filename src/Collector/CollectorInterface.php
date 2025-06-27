<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Collector;

interface CollectorInterface
{
    public function name(): string;

    public function templatePath(): string;

    /**
     * @return array<string, mixed>
     */
    public function collect(): array;
}
