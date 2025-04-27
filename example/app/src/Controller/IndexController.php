<?php
declare(strict_types=1);

namespace App\Controller;

use Phalcon\Logger\Logger;
use Phalcon\Mvc\Controller;
use Srgiz\Phalcon\WebProfiler\Service\Stopwatch;

/**
 * @property-read Logger $logger
 * @property-read Stopwatch|null $stopwatch
 */
class IndexController extends Controller
{
    /**
     * @Get('/', name='home')
     */
    public function indexAction(): void
    {
        $this->stopwatch?->start('metric');
        $this->logger->debug('start', ['action' => 'index']);

        // data
        usleep($usleep = rand(500, 1000));

        $this->logger->info('usleep: %usleep%', ['usleep' => $usleep, 'action' => 'index']);
        $this->logger->debug('stop', ['action' => 'index']);
        $this->stopwatch?->stop('metric');

        // render
    }

    /**
     * @Get('/test', name='test')
     */
    public function testAction(): void
    {
        throw new \LogicException('Test');
    }
}
