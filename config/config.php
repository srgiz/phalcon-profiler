<?php

declare(strict_types=1);

use Phalcon\Config\Config;

return new Config([
    'viewsCachePath' => null,
    'tagsDir' => '/var/www/var/profiler',
    'routePrefix' => '/_profiler',
    'collectors' => [],
    'storageDateInterval' => 'P2D',
    'excludeRoutes' => [
        '/favicon.ico',
        '/.well-known/appspecific/com.chrome.devtools.json',
    ],
]);
