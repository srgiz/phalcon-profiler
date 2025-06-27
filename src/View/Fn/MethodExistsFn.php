<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\View\Fn;

class MethodExistsFn
{
    public static function execute(object $obj, string $name): bool
    {
        return method_exists($obj, $name);
    }
}
