<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\View;

class Volt extends \Phalcon\Mvc\View\Engine\Volt
{
    public function setCompiler(Compiler $compiler): void
    {
        $this->compiler = $compiler;
        $this->compiler->setOptions($this->getOptions());
    }
}
