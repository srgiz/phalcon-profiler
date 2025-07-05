<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Tests\Service;

use PHPUnit\Framework\TestCase;
use Srgiz\Phalcon\WebProfiler\Service\DataReader;
use Srgiz\Phalcon\WebProfiler\Service\DataWriter;

class DataTest extends TestCase
{
    public function testWriteRead(): void
    {
        $filename = realpath(__DIR__.'/../../files').'/'.uniqid().'.xml';

        $writer = new DataWriter($filename);
        $writer->add('_meta', ['statusCode' => 201]);
        $writer->add('other name', ['meta' => ['count' => 2]]);
        unset($writer);

        $reader = new DataReader($filename);
        $this->assertSame(201, $reader->read('_meta')['statusCode']);
        $this->assertSame(2, $reader->read('other name')['meta']['count']);
        $this->assertSame([], $reader->read('unknown'));

        unlink($filename);
    }

    public function testReadFileNotFound(): void
    {
        $filename = realpath(__DIR__.'/../../files').'/no.xml';
        $this->expectException(\Phalcon\Mvc\Router\Exception::class);
        new DataReader($filename);
    }

    public function testReadInvalidFile(): void
    {
        $filename = realpath(__DIR__.'/../../files').'/invalid.xml';
        file_put_contents($filename, 'text');
        $reader = new DataReader($filename);

        try {
            $this->expectException(\Phalcon\Mvc\Router\Exception::class);
            $reader->read('_meta');
        } finally {
            unlink($filename);
        }
    }

    public function testWriterClosed(): void
    {
        $reflection = new \ReflectionClass(DataWriter::class);
        $property = $reflection->getProperty('xml');

        if (PHP_VERSION_ID < 80100) {
            $property->setAccessible(true);
        }

        $filename = realpath(__DIR__.'/../../files').'/invalid.xml';

        try {
            $reader = new DataWriter($filename);

            /** @var array<string, \SplPriorityQueue> $events */
            $resource = $property->getValue($reader);
            $this->assertIsResource($resource);

            fclose($resource);
            unset($writer); // assert no exception
        } finally {
            unlink($filename);
        }
    }
}
