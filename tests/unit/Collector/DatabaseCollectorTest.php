<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Tests\Collector;

use Phalcon\Db\Adapter\AdapterInterface;
use Phalcon\Db\Profiler;
use Phalcon\Events\Event;
use PHPUnit\Framework\TestCase;
use Srgiz\Phalcon\WebProfiler\Collector\DatabaseCollector;

class DatabaseCollectorTest extends TestCase
{
    public function test(): void
    {
        $collector = new DatabaseCollector(new Profiler());
        $data = $collector->collect();
        $this->assertCount(0, $data['connections']);
        $this->assertSame(.0, $data['time']);
        $this->assertSame(0, $data['meta']['count']);

        $adapter = $this->createConfiguredMock(AdapterInterface::class, [
            'getConnectionId' => '2',
            'getType' => 'mysql',
            'getSQLStatement' => 'SELECT 1 WHERE 1=:n',
            'getSQLVariables' => ['n' => 1],
            'getSQLBindTypes' => [],
        ]);

        $collector->beginTransaction(new Event('beginTransaction'), $adapter);
        $collector->beforeQuery(new Event('beforeQuery'), $adapter);
        $collector->afterQuery(new Event('afterQuery'), $adapter);
        $collector->commitTransaction(new Event('commitTransaction'), $adapter);

        $data2 = $collector->collect();
        $this->assertSame(3, $data2['meta']['count']);
        $this->assertCount(1, $data2['connections']);
        $this->assertCount(3, $data2['connections'][2]['queries']);
        $this->assertSame('mysql', $data2['connections'][2]['type']);
    }
}
