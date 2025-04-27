<?php
declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Controller;

use Phalcon\Http\Response;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Controller;
use Srgiz\Phalcon\WebProfiler\Service\Manager;
use Srgiz\Phalcon\WebProfiler\View\View;

/**
 * @property-read Manager profilerManager
 */
class ProfilerController extends Controller
{
    public function indexAction(): ResponseInterface
    {
        return $this->render('@profiler/profiler/requests', ['requests' => $this->profilerManager->requests()]);
    }

    public function tagAction(string $tag): ResponseInterface
    {
        $panel = $this->request->get('panel', null, '');
        $data = $this->profilerManager->data($tag, $panel);
        return $this->render($data['_templatePath'], $data);
    }

    public function barAction(string $tag): ResponseInterface
    {
        try {
            return $this->render('@profiler/bar', $this->profilerManager->bar($tag));
        } catch (\Throwable $e) {
            return (new Response())->setStatusCode(500, $e->getMessage());
        }
    }

    private function render(string $path, array $params): ResponseInterface
    {
        /** @var View $view */
        $view = $this->getDI()->getShared('profilerView');
        return new Response($view->render($view->preparePath($path), $params));
    }
}
