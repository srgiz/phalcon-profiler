<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Service;

class DataWriter
{
    /** @var resource */
    private $xml;

    public function __construct(string $filename)
    {
        $this->xml = fopen($filename, 'w');
        fwrite($this->xml, '<?xml version="1.0" encoding="UTF-8"?><profiler>');
    }

    public function add(string $name, array $data): void
    {
        fwrite($this->xml, sprintf('<%1$s>%2$s</%1$s>', $name, base64_encode(
            (fn (string $serialize) => function_exists('gzencode') ? gzencode($serialize, 3) : $serialize)(serialize($data))
        )));
    }

    public function __destruct()
    {
        try {
            fwrite($this->xml, '</profiler>');
        } catch (\Throwable $e) {
        }
    }
}
