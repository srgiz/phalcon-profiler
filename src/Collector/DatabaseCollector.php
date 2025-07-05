<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Collector;

use Phalcon\Db\Adapter\AdapterInterface;
use Phalcon\Db\Profiler;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Events\EventInterface;

class DatabaseCollector implements CollectorInterface
{
    private \WeakMap $weakMap;

    public function __construct(
        private Profiler $profiler,
    ) {
        $this->weakMap = new \WeakMap();
    }

    public function templatePath(): string
    {
        return '@profiler/profiler/database';
    }

    public function menuPath(): string
    {
        return '@profiler/profiler/database.menu';
    }

    public function name(): string
    {
        return 'Database';
    }

    public function collect(): array
    {
        $queries = $this->profiler->getProfiles() ?? [];
        $connections = [];

        foreach ($queries as $index => $query) {
            $options = $this->weakMap[$query];

            if (!isset($connections[$options['connId']])) {
                $connections[$options['connId']] = [
                    'type' => $options['type'],
                ];
            }

            $connections[$options['connId']]['queries'][] = [
                'query' => $query,
                'backtrace' => $options['backtrace'],
            ];
        }

        return [
            'connections' => $connections,
            'time' => $this->profiler->getTotalElapsedSeconds() * 1000,
            'meta' => [
                'count' => count($queries),
            ],
        ];
    }

    // app | micro
    public function boot(EventInterface $event, InjectionAwareInterface $app): bool
    {
        $this->profiler->reset();

        return true;
    }

    public function beforeQuery(EventInterface $event, AdapterInterface $conn): void
    {
        $this->profiler->startProfile($conn->getSQLStatement(), $conn->getSQLVariables(), $conn->getSQLBindTypes());

        $this->weakMap[$this->profiler->getLastProfile()] = [
            'connId' => $conn->getConnectionId(),
            'type' => $conn->getType(),
            'backtrace' => array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 3),
        ];
    }

    public function afterQuery(EventInterface $event, AdapterInterface $conn): void
    {
        $this->profiler->stopProfile();
    }

    public function beginTransaction(EventInterface $event, AdapterInterface $conn): void
    {
        $this->profiler->startProfile(match ($event->getType()) {
            'beginTransaction' => 'BEGIN',
            'commitTransaction' => 'COMMIT',
            default => 'ROLLBACK',
        });

        $this->weakMap[$this->profiler->getLastProfile()] = [
            'connId' => $conn->getConnectionId(),
            'type' => $conn->getType(),
            'backtrace' => array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 3),
        ];

        $this->profiler->stopProfile();
    }

    public function commitTransaction(EventInterface $event, AdapterInterface $conn): void
    {
        $this->beginTransaction($event, $conn);
    }

    public function rollbackTransaction(EventInterface $event, AdapterInterface $conn): void
    {
        $this->beginTransaction($event, $conn);
    }
}
