<?php

declare(strict_types=1);

use App\Profiler\CustomCollector;
use Phalcon\Config\Config;

return new Config([
    'application' => [
        // views
        'viewsDir' => dirname(__DIR__).'/templates',
        'viewsCachePath' => dirname(__DIR__).'/var/cache/volt/',
    ],
    'database' => [
        'host' => 'postgres',
        'username' => 'postgres',
        'password' => 'root',
        'dbname' => 'postgres',
    ],
    'profiler' => [
        'viewsCachePath' => dirname(__DIR__).'/var/cache/volt/',
        'tagsDir' => dirname(__DIR__).'/var/profiler',
        'collectors' => [
            CustomCollector::class,
        ],
        //'excludeRoutes' => ['/404'],
    ],
]);
