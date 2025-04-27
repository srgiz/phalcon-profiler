<?php
declare(strict_types=1);

namespace App;

use Exception;
use Phalcon\Events\EventInterface;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Dispatcher;

class BeforeExceptionListener
{
    public function beforeException(EventInterface $event, Dispatcher $dispatcher, Exception $exception): bool
    {
        /** @var ResponseInterface $response */
        $response = $dispatcher->getDI()->getShared('response');
        $response->setStatusCode(500);

        return false;
    }
}
