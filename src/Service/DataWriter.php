<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Service;

class DataWriter
{
    private \ZipArchive $archive;

    public function __construct(string $filename)
    {
        $this->archive = new \ZipArchive();
        $this->archive->open($filename, \ZipArchive::CREATE);
    }

    public function add(string $name, array $data): void
    {
        $this->archive->addFromString($name, serialize($data), \ZipArchive::FL_OVERWRITE | \ZipArchive::FL_ENC_UTF_8);
    }

    public function __destruct()
    {
        $this->archive->close();
    }
}
