<?php
declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Collector;

use Phalcon\Events\EventInterface;
use Phalcon\Mvc\View\Engine\AbstractEngine;

class ViewCollector implements CollectorInterface
{
    private array $data = [
        'activeRenderPaths' => [],
    ];

    public function afterCompile(EventInterface $event, AbstractEngine $engine): bool
    {
        if (method_exists($engine->getView(), 'getActiveRenderPath')) {
            $activeRenderPath = $engine->getView()->getActiveRenderPath();

            if (is_array($activeRenderPath)) {
                $activeRenderPath = current($activeRenderPath);
            }

            $this->data['activeRenderPaths'][] = [
                'path' => $activeRenderPath,
                'backtrace' => array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 3),
            ];
        }

        return true;
    }

    public function templatePath(): string
    {
        return '@profiler/profiler/view';
    }

    public function name(): string
    {
        return 'View';
    }

    public function icon(): string
    {
        return <<<HTML
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-layers" viewBox="0 0 16 16">
    <path d="M8.235 1.559a.5.5 0 0 0-.47 0l-7.5 4a.5.5 0 0 0 0 .882L3.188 8 .264 9.559a.5.5 0 0 0 0 .882l7.5 4a.5.5 0 0 0 .47 0l7.5-4a.5.5 0 0 0 0-.882L12.813 8l2.922-1.559a.5.5 0 0 0 0-.882l-7.5-4zm3.515 7.008L14.438 10 8 13.433 1.562 10 4.25 8.567l3.515 1.874a.5.5 0 0 0 .47 0l3.515-1.874zM8 9.433 1.562 6 8 2.567 14.438 6 8 9.433z"/>
</svg>
HTML;
    }

    public function collect(): array
    {
        return $this->data;
    }
}
