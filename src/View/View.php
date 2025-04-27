<?php
declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\View;

use Phalcon\Mvc\View\Simple;

class View extends Simple
{
    public function partial(string $partialPath, $params = null): void
    {
        parent::partial($this->preparePath($partialPath), $params);
    }

    public function preparePath(string $path): string
    {
        return str_replace('@profiler', realpath(__DIR__ . '/../../templates'), $path);
    }
}
