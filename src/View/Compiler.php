<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\View;

/**
 * @property View view
 */
class Compiler extends \Phalcon\Mvc\View\Engine\Volt\Compiler
{
    protected function getFinalPath(string $path): string
    {
        /**
         * @psalm-suppress PossiblyNullReference
         * @psalm-suppress UndefinedInterfaceMethod
         */
        return $this->view->preparePath((string) parent::getFinalPath($path));
    }
}
