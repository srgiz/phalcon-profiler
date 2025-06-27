<?php

declare(strict_types=1);

namespace App;

use Phalcon\Events\EventInterface;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Dispatcher;

class BeforeExceptionListener
{
    public function beforeException(EventInterface $event, Dispatcher $dispatcher, \Exception $exception): bool
    {
        /** @var ResponseInterface $response */
        $response = $dispatcher->getDI()->getShared('response');

        if ($exception instanceof Dispatcher\Exception) {
            $response->setStatusCode(404);
        } else {
            $response->setStatusCode(500);
        }

        return false;
    }
}
