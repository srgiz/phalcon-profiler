<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Collector;

use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Events\EventInterface;
use Phalcon\Http\RequestInterface;
use Phalcon\Http\ResponseInterface;

class RequestCollector implements CollectorInterface
{
    private ?RequestInterface $request = null;

    private ?ResponseInterface $response = null;

    // app
    public function boot(EventInterface $event, InjectionAwareInterface $app): bool
    {
        $this->request = null;
        $this->response = null;

        return true;
    }

    // app
    public function beforeSendResponse(EventInterface $event, InjectionAwareInterface $app, ?ResponseInterface $response): void
    {
        $this->request = $app->getDI()->getShared('request');
        $this->response = $response;
    }

    // micro
    public function beforeHandleRoute(EventInterface $event, InjectionAwareInterface $app): void
    {
        $this->beforeSendResponse($event, $app, null);
    }

    // micro
    public function afterHandleRoute(EventInterface $event, InjectionAwareInterface $app, mixed $returnedValue): void
    {
        $this->beforeSendResponse($event, $app, $returnedValue instanceof ResponseInterface ? $returnedValue : null);
    }

    public function templatePath(): string
    {
        return '@profiler/profiler/request';
    }

    public function menuPath(): string
    {
        return '@profiler/profiler/request.menu';
    }

    public function name(): string
    {
        return 'Request';
    }

    public function collect(): array
    {
        return [
            'query' => $this->request?->getQuery() ?? [],
            'post' => $this->request?->getPost() ?? [],
            'requestHeaders' => $this->request?->getHeaders() ?? [],
            'responseHeaders' => $this->response?->getHeaders()->toArray() ?? [],
        ];
    }
}
