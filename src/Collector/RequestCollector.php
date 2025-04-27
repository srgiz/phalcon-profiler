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

    public function beforeSendResponse(EventInterface $event, InjectionAwareInterface $app, ResponseInterface $response): void
    {
        $this->request = $app->getDI()->getShared('request');
        $this->response = $response;
    }

    public function templatePath(): string
    {
        return '@profiler/profiler/request';
    }

    public function name(): string
    {
        return 'Request';
    }

    public function icon(): string
    {
        return <<<HTML
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-app" viewBox="0 0 16 16">
    <path d="M11 2a3 3 0 0 1 3 3v6a3 3 0 0 1-3 3H5a3 3 0 0 1-3-3V5a3 3 0 0 1 3-3h6zM5 1a4 4 0 0 0-4 4v6a4 4 0 0 0 4 4h6a4 4 0 0 0 4-4V5a4 4 0 0 0-4-4H5z"/>
</svg>
HTML;
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
