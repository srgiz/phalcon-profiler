<?php
declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Service;

use Phalcon\Assets\Collection;
use Phalcon\Assets\Inline;
use Phalcon\Assets\Manager;

class Assets extends Manager
{
    protected $implicitOutput = false;

    public function outputInlineFile(string $path): string
    {
        $path = $this->preparePath($path);
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $assetClass = '\Phalcon\Assets\Inline\\' . ucfirst($ext);
        /** @var Inline $asset */
        $asset = new $assetClass(file_get_contents($path));
        return $this->outputInline((new Collection())->addInline($asset), 'js' === $ext ? 'script' : 'style');
    }

    private function preparePath(string $path): string
    {
        return str_replace('@profiler', realpath(__DIR__ . '/../../'), $path);
    }
}
