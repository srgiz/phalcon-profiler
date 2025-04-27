<?php

use Phalcon\Config\Config;

return new Config([
    'viewsCachePath' => null,
    'tagsDir' => '/var/www/var/profiler',
    'routePrefix' => '/_profiler',
    'collectors' => [],
]);
