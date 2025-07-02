<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Service;

use Phalcon\Mvc\Router\Exception as RouterException;

class DataReader
{
    private string $filename;

    /**
     * @throws RouterException
     */
    public function __construct(string $filename)
    {
        if (!file_exists($filename)) {
            throw new RouterException(sprintf('File "%s" not found', $filename));
        }

        $this->filename = $filename;
    }

    /**
     * @throws RouterException
     */
    public function read(string $name): array
    {
        $reader = new \XMLReader();

        try {
            $reader->open($this->filename);

            while ($reader->read()) {
                if ($reader->localName === $name) {
                    $data = base64_decode($reader->readInnerXml());

                    if (function_exists('gzdecode')) {
                        /** @psalm-suppress RiskyTruthyFalsyComparison */
                        $data = @gzdecode($data) ?: $data;
                    }

                    return unserialize($data);
                }
            }

            return [];
        } catch (\Throwable $e) {
            throw new RouterException($e->getMessage());
        } finally {
            $reader->close();
        }
    }
}
