<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Tests\App;

use Phalcon\Events\Manager;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Router;
use PHPUnit\Framework\TestCase;
use Srgiz\Phalcon\WebProfiler\Tests\PhalconTestCaseTrait;

class MicroTest extends TestCase
{
    use PhalconTestCaseTrait;

    protected Micro $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDi([
            'profiler' => [
                'tagsDir' => realpath(__DIR__.'/../../files'),
                'viewsCachePath' => $cacheVolt = realpath(__DIR__.'/../../files').'/cache.volt/',
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

        if (!is_dir($cacheVolt)) {
            mkdir($cacheVolt, 0755);
        }

        $this->app = new Micro($this->di);
        $this->app->setEventsManager($this->di->get('eventsManager'));
    }

    public function pages(): array
    {
        return [
            ['/_profiler', false],
            ['/_profiler/tag/', true],
            ['/_profiler/bar/', true],
        ];
    }

    /**
     * @dataProvider pages
     */
    public function testProfilerPage(string $pattern, bool $withTag): void
    {
        /** @var \Srgiz\Phalcon\WebProfiler\Service\Manager $manager */
        $manager = $this->di->get('profilerManager');
        $manager->save($tag = uniqid(), new \DateTimeImmutable(), $this->app, new Response(null, 201));

        try {
            ob_start();
            $response = $this->app->handle($pattern.($withTag ? $tag : ''));
            ob_get_clean();

            $this->assertInstanceOf(ResponseInterface::class, $response);
            $this->assertSame(200, $response->getStatusCode());
        } finally {
            unlink(realpath(__DIR__.'/../../files').'/'.$tag.'.xml');
        }
    }

    public function notFound(): array
    {
        return [
            'tag' => ['/_profiler/tag/abc', 422],
            'bar' => ['/_profiler/bar/def', 500],
        ];
    }

    /**
     * @dataProvider notFound
     */
    public function testNotFound(string $pattern, int $statusCode): void
    {
        ob_start();
        $response = $this->app->handle($pattern);
        ob_get_clean();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame($statusCode, $response->getStatusCode());
    }

    public function testPhpInfo(): void
    {
        ob_start();
        $response = $this->app->handle('/_profiler/phpinfo');
        ob_get_clean();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(200, $response->getStatusCode());
    }
}
