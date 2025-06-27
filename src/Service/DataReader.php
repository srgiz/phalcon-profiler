<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Service;

class DataReader
{
    private \ZipArchive $archive;

    public function __construct(string $filename)
    {
        $this->archive = new \ZipArchive();
        $this->archive->open($filename, \ZipArchive::RDONLY);
    }

    public function read(string $name): array
    {
        $value = $this->archive->getFromName($name);

        if (false === $value) {
            return [];
        }

        return unserialize($value);
    }

    public function __destruct()
    {
        $this->archive->close();
    }
}
