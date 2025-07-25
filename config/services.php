<?php

declare(strict_types=1);

return [
    'profilerVersion' => [
        'className' => 'Srgiz\Phalcon\WebProfiler\Service\Version',
        'shared' => true,
    ],
    'profilerView' => [
        'className' => 'Srgiz\Phalcon\WebProfiler\View\View',
        'shared' => true,
        'calls' => [
            [
                'method' => 'registerEngines',
                'arguments' => [
                    ['type' => 'parameter', 'value' => ['.volt' => 'profilerVolt']],
                ],
            ],
        ],
    ],
    'profilerVoltCompiler' => [
        'className' => 'Srgiz\Phalcon\WebProfiler\View\Compiler',
        'shared' => true,
        'arguments' => [
            ['type' => 'service', 'name' => 'profilerView'],
        ],
        'calls' => [
            [
                'method' => 'addFunction',
                'arguments' => [
                    ['type' => 'parameter', 'value' => 'profiler_dump'],
                    ['type' => 'parameter', 'value' => 'Srgiz\Phalcon\WebProfiler\View\Fn\DumpFn::execute'],
                ],
            ],
            [
                'method' => 'addFunction',
                'arguments' => [
                    ['type' => 'parameter', 'value' => 'method_exists'],
                    ['type' => 'parameter', 'value' => 'method_exists'],
                ],
            ],
            [
                'method' => 'addFunction',
                'arguments' => [
                    ['type' => 'parameter', 'value' => 'str_starts_with'],
                    ['type' => 'parameter', 'value' => 'str_starts_with'],
                ],
            ],
        ],
    ],
    'profilerTag' => [
        'className' => 'Phalcon\Html\TagFactory',
        'shared' => true,
        'arguments' => [
            ['type' => 'service', 'name' => 'escaper'],
        ],
    ],
    'profilerAssets' => [
        'className' => 'Srgiz\Phalcon\WebProfiler\Service\Assets',
        'shared' => true,
        'arguments' => [
            ['type' => 'service', 'name' => 'profilerTag'],
        ],
    ],
    'profilerDump' => [
        'className' => 'Phalcon\Support\Debug\Dump',
        'shared' => true,
        'arguments' => [
            ['type' => 'parameter', 'value' => [
                'pre' => 'margin-bottom: 0',
                'other' => '',
                'arr' => 'color: var(--bs-emphasis-color)',
                'obj' => 'color: var(--bs-emphasis-color)',
                'bool' => 'color: var(--bs-warning)',
                'null' => 'color: var(--bs-warning)',
                'float' => 'color: var(--bs-info)',
                'int' => 'color: var(--bs-info)',
                'num' => 'color: var(--bs-info)',
                'str' => 'color: var(--bs-teal)',
                'res' => 'color: var(--bs-teal)',
            ]],
            ['type' => 'parameter', 'value' => true],
        ],
    ],
    'profilerStopwatch' => [
        'className' => 'Srgiz\Phalcon\WebProfiler\Service\Stopwatch',
        'shared' => true,
    ],
    'profilerDb' => [
        'className' => 'Phalcon\Db\Profiler',
        'shared' => true,
    ],
    'profilerLoggerAdapter' => [
        'className' => 'Srgiz\Phalcon\WebProfiler\Logger\ProfilerAdapter',
        'shared' => true,
        'arguments' => [
            ['type' => 'service', 'name' => 'eventsManager'],
        ],
    ],
    'profilerManager' => [
        'className' => 'Srgiz\Phalcon\WebProfiler\Service\Manager',
        'shared' => true,
    ],
    'Srgiz\Phalcon\WebProfiler\Controller\ProfilerController' => [
        'className' => 'Srgiz\Phalcon\WebProfiler\Controller\ProfilerController',
        'shared' => true,
    ],
    'Srgiz\Phalcon\WebProfiler\Collector\RequestCollector' => [
        'className' => 'Srgiz\Phalcon\WebProfiler\Collector\RequestCollector',
        'shared' => true,
    ],
    'Srgiz\Phalcon\WebProfiler\Collector\PerformanceCollector' => [
        'className' => 'Srgiz\Phalcon\WebProfiler\Collector\PerformanceCollector',
        'shared' => true,
        'arguments' => [
            ['type' => 'service', 'name' => 'profilerStopwatch'],
        ],
    ],
    'Srgiz\Phalcon\WebProfiler\Collector\DatabaseCollector' => [
        'className' => 'Srgiz\Phalcon\WebProfiler\Collector\DatabaseCollector',
        'shared' => true,
        'arguments' => [
            ['type' => 'service', 'name' => 'profilerDb'],
        ],
    ],
    'Srgiz\Phalcon\WebProfiler\Collector\LoggerCollector' => [
        'className' => 'Srgiz\Phalcon\WebProfiler\Collector\LoggerCollector',
        'shared' => true,
    ],
    'Srgiz\Phalcon\WebProfiler\Collector\ExceptionCollector' => [
        'className' => 'Srgiz\Phalcon\WebProfiler\Collector\ExceptionCollector',
        'shared' => true,
    ],
    'Srgiz\Phalcon\WebProfiler\Collector\RoutingCollector' => [
        'className' => 'Srgiz\Phalcon\WebProfiler\Collector\RoutingCollector',
        'shared' => true,
        'arguments' => [
            ['type' => 'service', 'name' => 'router'],
        ],
    ],
    'Srgiz\Phalcon\WebProfiler\Collector\EventsCollector' => [
        'className' => 'Srgiz\Phalcon\WebProfiler\Collector\EventsCollector',
        'shared' => true,
        'arguments' => [
            ['type' => 'service', 'name' => 'eventsManager'],
        ],
    ],
    'Srgiz\Phalcon\WebProfiler\Collector\VoltCollector' => [
        'className' => 'Srgiz\Phalcon\WebProfiler\Collector\VoltCollector',
        'shared' => true,
    ],
];
