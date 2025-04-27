<?php
declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Service;

use ZipArchive;

class DataReader
{
    private ZipArchive $archive;

    public function __construct(string $filename)
    {
        $this->archive = new ZipArchive();
        $this->archive->open($filename, ZipArchive::RDONLY);
    }

    public function read(string $name): array
    {
        return unserialize($this->archive->getFromName($name));
    }

    public function __destruct()
    {
        $this->archive->close();
    }
}
