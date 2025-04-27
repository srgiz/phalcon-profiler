<?php

use App\Profiler\CustomCollector;
use Phalcon\Config\Config;

return new Config([
    'application' => [
        // views
        'viewsDir' => dirname(__DIR__) . '/templates',
        'viewsCachePath' => dirname(__DIR__) . '/var/cache/volt/',
    ],
    'profiler' => [
        'viewsCachePath' => dirname(__DIR__) . '/var/cache/volt/',
        'tagsDir' => dirname(__DIR__) . '/var/profiler',
        'collectors' => [
            CustomCollector::class,
        ],
    ],
]);
