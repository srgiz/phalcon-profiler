<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Tests\Service;

use Phalcon\Events\Manager;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Router;
use PHPUnit\Framework\TestCase;
use Srgiz\Phalcon\WebProfiler\Service\Manager as Manage;
use Srgiz\Phalcon\WebProfiler\Tests\PhalconTestCaseTrait;

class ManagerTest extends TestCase
{
    use PhalconTestCaseTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDi([
            'profiler' => [
                'tagsDir' => realpath(__DIR__.'/../../files'),
            ],
        ], [
            'eventsManager' => [
                'className' => Manager::class,
                'shared' => true,
                'calls' => [
                    ['method' => 'enablePriorities', 'arguments' => [['type' => 'parameter', 'value' => true]]],
                ],
            ],
            'request' => [
                'className' => Request::class,
                'shared' => true,
            ],
            'router' => [
                'className' => Router::class,
                'shared' => true,
            ],
        ]);

        $_SERVER['REQUEST_URI'] = '/route';
    }

    protected function tearDown(): void
    {
        unset($_SERVER['REQUEST_URI']);
        parent::tearDown();
    }

    public function testCollectors(): void
    {
        $manager = new Manage();
        $manager->setDI($this->di);

        $this->assertCount(8, $manager->collectors());
    }

    public function testSave(): void
    {
        $manager = new Manage();
        $manager->setDI($this->di);
        $manager->save($tag = uniqid(), new \DateTimeImmutable(), $manager, new Response(null, 201));

        $this->assertFileExists($filename = realpath(__DIR__.'/../../files').'/'.$tag.'.xml');
        unlink($filename);
    }

    public function testRemoveExpiredTags(): void
    {
        $manager = new Manage();
        $manager->setDI($this->di);
        $manager->save($tag = uniqid(), $time = new \DateTimeImmutable(), $manager, new Response(null, 201));
        $dir = realpath(__DIR__.'/../../files');
        $filename0 = $dir.'/'.$tag.'.xml';

        try {
            $requests = $manager->requests();
            $this->assertCount(1, $requests);
            $this->assertSame('GET', $requests[$tag]['method']);
            $this->assertSame('/route', $requests[$tag]['uri']);
            $this->assertSame(201, $requests[$tag]['statusCode']);
            $this->assertSame($time->format('c'), $requests[$tag]['requestTime']->format('c'));
            $this->assertSame(null, $requests[$tag]['route']);
            $this->assertSame([
                'Performance' => ['max' => .0],
                'Logs' => ['count' => 0],
                'Exception' => ['count' => 0],
                'Database' => ['count' => 0],
                'Events' => ['arePrioritiesEnabled' => true],
            ], $requests[$tag]['collectors']);

            $mt1 = (float) (new \DateTimeImmutable('-2 days'))->format('U.u');
            file_put_contents($filename1 = $dir.'/'.sprintf('%8x%05x', floor($mt1), ($mt1 - floor($mt1)) * 1000000).'.xml', 'r1');
            $this->assertFileExists($filename1);

            $mt2 = (float) (new \DateTimeImmutable('-3 days'))->format('U.u');
            file_put_contents($filename2 = $dir.'/'.sprintf('%8x%05x', floor($mt2), ($mt2 - floor($mt2)) * 1000000).'.xml', 'r2');
            $this->assertFileExists($filename2);

            $this->assertCount(1, $manager->requests());
            $this->assertFileExists($filename0);
            $this->assertFileDoesNotExist($filename1);
            $this->assertFileDoesNotExist($filename2);
        } finally {
            unlink($filename0);
        }
    }

    public function testData(): void
    {
        $manager = new Manage();
        $manager->setDI($this->di);
        $manager->save($tag = uniqid(), $time = new \DateTimeImmutable(), $manager, new Response(null, 201));

        $data = $manager->bar($tag);
        $this->assertSame('/route', $data['_meta']['uri']);

        $data2 = $manager->data('latest', 'Events');
        $this->assertNotEmpty($data2['listeners']);
        $this->assertSame(true, $data2['meta']['arePrioritiesEnabled']);
        $this->assertSame('/route', $data2['_meta']['uri']);
        $this->assertSame($tag, $data2['_tag']);
        $this->assertSame('Events', $data2['_panel']);
        $this->assertSame('@profiler/profiler/events', $data2['_templatePath']);

        unlink(realpath(__DIR__.'/../../files').'/'.$tag.'.xml');
    }
}
