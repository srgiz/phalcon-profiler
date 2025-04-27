<?php
declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Logger;

use Phalcon\Events\ManagerInterface;
use Phalcon\Logger\Adapter\AbstractAdapter;
use Phalcon\Logger\Item;

class ProfilerAdapter extends AbstractAdapter
{
    protected $defaultFormatter = ProfilerLineFormatter::class;

    public function __construct(private ManagerInterface $eventsManager) {}

    public function process(Item $item): void
    {
        $this->eventsManager->fire('profiler:log', $this, $item);
    }

    public function close(): bool
    {
        return true;
    }
}
