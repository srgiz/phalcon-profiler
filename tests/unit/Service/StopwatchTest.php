<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Tests\Service;

use PHPUnit\Framework\TestCase;
use Srgiz\Phalcon\WebProfiler\Service\Stopwatch;

class StopwatchTest extends TestCase
{
    public function testEvents(): void
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('app');
        $stopwatch->start('test');
        $stopwatch->start('e');
        $stopwatch->stop('e');
        $stopwatch->stop('app');
        $stopwatch->stop('test');
        $stopwatch->start('test');
        $stopwatch->stop('test');
        $events = $stopwatch->events();

        $this->assertCount(1, $events['app']);
        $this->assertCount(2, $events['test']);
        $this->assertCount(1, $events['e']);

        $stopwatch->reset();
        $this->assertCount(0, $stopwatch->events());
    }

    public function testStopException(): void
    {
        $this->expectException(\Throwable::class);
        $stopwatch = new Stopwatch();
        $stopwatch->stop('test');
    }

    public function testFinal(): void
    {
        $stopwatch = new Stopwatch();

        $final = $stopwatch->final(false);
        $this->assertSame($final, $stopwatch->final(false));
        $this->assertNotSame($final, $final2 = $stopwatch->final(true));
        $this->assertSame($final2, $stopwatch->final(false));
    }
}
