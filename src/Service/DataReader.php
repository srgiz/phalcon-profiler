<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Service;

use Phalcon\Mvc\Router\Exception as RouterException;

class DataReader
{
    private \ZipArchive $archive;

    /**
     * @throws RouterException
     */
    public function __construct(string $filename)
    {
        if (!file_exists($filename)) {
            throw new RouterException(sprintf('File "%s" not found', $filename));
        }

        $this->archive = new \ZipArchive();
        $this->archive->open($filename, \ZipArchive::RDONLY);
    }

    /**
     * @throws RouterException
     */
    public function read(string $name): array
    {
        try {
            $value = $this->archive->getFromName($name);
        } catch (\ValueError $e) {
            throw new RouterException($e->getMessage());
        }

        if (false === $value) {
            return [];
        }

        return unserialize($value);
    }

    public function __destruct()
    {
        try {
            $this->archive->close();
        } catch (\Throwable $e) {
        }
    }
}
