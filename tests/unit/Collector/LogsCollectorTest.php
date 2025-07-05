<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Tests\Collector;

use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Events\Event;
use Phalcon\Logger\AbstractLogger;
use Phalcon\Logger\Adapter\Noop;
use Phalcon\Logger\Item;
use PHPUnit\Framework\TestCase;
use Srgiz\Phalcon\WebProfiler\Collector\LogsCollector;
use Srgiz\Phalcon\WebProfiler\Logger\ProfilerLineFormatter;

class LogsCollectorTest extends TestCase
{
    public function testCollect(): void
    {
        $collector = new LogsCollector();

        $this->assertSame([
            'items' => [],
            'buttons' => [],
            'meta' => ['count' => 0],
        ], $collector->collect());

        $collector->log(new Event('log'), $adapter = new Noop(), new Item(
            'Info',
            'INFO',
            AbstractLogger::INFO,
            new \DateTimeImmutable(),
            ['action' => 'test']
        ));

        $collector->log(new Event('log'), $adapter, new Item(
            'Info2',
            'INFO',
            AbstractLogger::INFO,
            new \DateTimeImmutable(),
            ['message' => 'test2']
        ));

        $collector->log(new Event('log'), $adapter, new Item(
            'Error',
            'ERROR',
            AbstractLogger::ERROR,
            new \DateTimeImmutable(),
            ['message' => 'test']
        ));

        $data = $collector->collect();
        $this->assertSame(['count' => 1], $data['meta']);

        $this->assertSame([
            AbstractLogger::ERROR => [
                'name' => 'ERROR',
                'count' => 1,
            ],
            AbstractLogger::INFO => [
                'name' => 'INFO',
                'count' => 2,
            ],
        ], $data['buttons']);

        $this->assertCount(3, $data['items']);

        $this->assertStringContainsString('Info', $data['items'][0]['message']);
        $this->assertSame(AbstractLogger::INFO, $data['items'][0]['level']);
        $this->assertSame('INFO', $data['items'][0]['levelName']);
        $this->assertInstanceOf(\DateTimeImmutable::class, $data['items'][0]['datetime']);
        $this->assertSame(['action' => 'test'], $data['items'][0]['context']);

        $collector->boot(new Event('log'), $this->createMock(InjectionAwareInterface::class));

        $this->assertSame([
            'items' => [],
            'buttons' => [],
            'meta' => ['count' => 0],
        ], $collector->collect());
    }

    public function logsFormatter(): array
    {
        return [
            [new Item(
                'Info message',
                'INFO',
                AbstractLogger::INFO,
                new \DateTimeImmutable(),
            ), '[<span style="color: var(--bs-emphasis-color)">INFO</span>] <span style="color: var(--bs-emphasis-color)">Info message</span>'],
            [new Item(
                'Warning message (%action%)',
                'WARNING',
                AbstractLogger::WARNING,
                new \DateTimeImmutable('-1 day'),
                ['action' => 'test'],
            ), '[<span style="color: var(--bs-emphasis-color)">WARNING</span>] <span style="color: var(--bs-emphasis-color)">Warning message (<span style="font-weight: 600">test</span>)</span>'],
            [new Item(
                'Debug message (%date2%)',
                'DEBUG',
                AbstractLogger::DEBUG,
                new \DateTimeImmutable(),
                ['date2' => new \DateTimeImmutable('-2 day')],
            ), '[<span style="color: var(--bs-emphasis-color)">DEBUG</span>] <span style="color: var(--bs-emphasis-color)">Debug message (%date2%)</span>'],
        ];
    }

    /**
     * @dataProvider logsFormatter
     */
    public function testFormatter(Item $item, string $message): void
    {
        $formatter = new ProfilerLineFormatter();
        $this->assertSame(
            sprintf('[<span style="color: var(--bs-emphasis-color)">%s</span>]%s', $item->getDateTime()->format('c'), $message),
            $formatter->format($item),
        );
    }
}
