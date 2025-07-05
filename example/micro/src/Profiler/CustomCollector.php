<?php

declare(strict_types=1);

namespace App\Profiler;

use Srgiz\Phalcon\WebProfiler\Collector\CollectorInterface;

class CustomCollector implements CollectorInterface
{
    public function templatePath(): string
    {
        return __DIR__.'/../../templates/profiler/custom';
    }

    public function name(): string
    {
        return 'Custom';
    }

    public function collect(): array
    {
        return [
            'message' => 'micro',
        ];
    }
}
