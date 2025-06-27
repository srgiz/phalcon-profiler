<?php

declare(strict_types=1);

namespace App\Controller;

use Phalcon\Logger\Logger;
use Phalcon\Mvc\Controller;
use Srgiz\Phalcon\WebProfiler\Service\Stopwatch;

/**
 * @property Logger $logger
 * @property Stopwatch|null $stopwatch
 */
class IndexController extends Controller
{
    /**
     * @Get('/', name='home')
     */
    public function indexAction(): void
    {
        //$this->stopwatch?->start('metric');
        $this->logger->debug('start', ['action' => 'index']);

        // data
        usleep($usleep = rand(20000, 100000));

        $this->db->query('select version() where 1=:n', ['n' => 1]);

        $this->logger->info('usleep: %usleep%', ['usleep' => $usleep, 'action' => 'index']);
        $this->logger->debug('stop', ['action' => 'index']);
        //$this->stopwatch?->stop('metric');

        // render
    }

    /**
     * @Get('/test', name='test')
     */
    public function testAction(): void
    {
        $this->logger->error('start', ['action' => 'test']);
        throw new \LogicException('Test');
    }
}
