<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Tests\App;

use Phalcon\Events\Manager;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\View;
use PHPUnit\Framework\TestCase;
use Srgiz\Phalcon\WebProfiler\Tests\PhalconTestCaseTrait;

class AppTest extends TestCase
{
    use PhalconTestCaseTrait;

    protected Application $app;

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
                'calls' => [
                    ['method' => 'add', 'arguments' => [
                        ['type' => 'parameter', 'value' => '/dummy'],
                        ['type' => 'parameter', 'value' => [
                            'namespace' => 'Srgiz\Phalcon\WebProfiler\Tests\App',
                            'controller' => 'Dummy',
                            'action' => 'view',
                        ]],
                        ['type' => 'parameter', 'value' => ['GET']],
                    ]],
                ],
            ],
            'dispatcher' => [
                'className' => Dispatcher::class,
                'shared' => true,
                'calls' => [
                    ['method' => 'setEventsManager', 'arguments' => [['type' => 'service', 'name' => 'eventsManager']]],
                ],
            ],
            'view' => [
                'className' => View::class,
                'shared' => true,
                'calls' => [
                    ['method' => 'setEventsManager', 'arguments' => [['type' => 'service', 'name' => 'eventsManager']]],
                    ['method' => 'setViewsDir', 'arguments' => [['type' => 'parameter', 'value' => __DIR__.'/app-templates']]],
                    ['method' => 'registerEngines', 'arguments' => [['type' => 'parameter', 'value' => ['.volt' => 'volt']]]],
                ],
            ],
            'volt' => [
                'className' => View\Engine\Volt::class,
                'shared' => true,
                'arguments' => [['type' => 'service', 'name' => 'view']],
                'calls' => [
                    ['method' => 'setEventsManager', 'arguments' => [['type' => 'service', 'name' => 'eventsManager']]],
                    ['method' => 'setOptions', 'arguments' => [['type' => 'parameter', 'value' => ['path' => $cacheVolt]]]],
                ],
            ],
            'response' => [
                'className' => Response::class,
                'shared' => true,
            ],
        ]);

        if (!is_dir($cacheVolt)) {
            mkdir($cacheVolt, 0755);
        }

        $this->app = new Application($this->di);
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

    public function testDummyController(): void
    {
        try {
            ob_start();
            $response = $this->app->handle('/dummy');
            ob_get_clean();

            $this->assertInstanceOf(ResponseInterface::class, $response);
            $this->assertSame(200, $response->getStatusCode() ?? 200);
            $this->assertSame('test tpl', $response->getContent());
        } finally {
            $files = glob(realpath(__DIR__.'/../../files').'/*.xml');

            foreach ($files as $file) {
                unlink($file);
            }
        }
    }
}

class DummyController extends Controller
{
    public function viewAction(): void
    {
        $this->view->render('dummy', 'view');
    }
}
