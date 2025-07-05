<?php

declare(strict_types=1);

use Phalcon\Autoload\Loader;
use Phalcon\Config\Config;
use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Response;
use Phalcon\Mvc\Micro;
//use Phalcon\Support\Debug;
use Srgiz\Phalcon\WebProfiler\WebProfiler;

set_error_handler(function (int $errno, string $message, string $file, int $line) {
    if (!(error_reporting() & $errno)) {
        return false;
    }

    switch ($errno) {
        case E_NOTICE:
        case E_WARNING:
            throw new ErrorException(message: $message, severity: $errno, filename: $file, line: $line);
    }

    return false;
});

$rootPath = dirname(__DIR__);

(new Loader())
    ->setNamespaces([
        'App' => $rootPath.'/src',
        'Srgiz\Phalcon\WebProfiler' => realpath(__DIR__.'/../../../src'),
    ])
    ->register()
;

$container = new FactoryDefault();
$config = require_once $rootPath.'/config/config.php';

$container->setShared('config', function () use ($config): Config {
    return $config;
});

$container->loadFromYaml($rootPath.'/config/services.yaml', [
    '!rootPath' => static function (string $filePath) use ($rootPath): string {
        // https://www.php.net/manual/en/yaml.callbacks.parse.php
        return $rootPath.$filePath;
    },
    '!config' => function (string $path) use ($config): mixed {
        return $config->path($path);
    },
]);

$container->register(new WebProfiler()); // if dev

$app = new Micro($container);
$app->setEventsManager($container->getShared('eventsManager'));

$app->notFound(function () {
    return new Response(null, 404);
});

$app->get(
    '/api/robots',
    function () use ($app) {
        $app->db->query('select version() where 1=:n', ['n' => 1]);

        /** @psalm-suppress UndefinedMagicPropertyFetch */
        $app->logger->debug('Message', ['action' => '/api/robots']);

        //echo json_encode(['ping' => 'pong']);
        return new Response(json_encode(['ping' => 'pong']));
    }
);

$app->get(
    '/api/robots/{id}',
    function (string $id) {
        throw new Exception('test');
    }
);

try {
    /** @psalm-suppress PossiblyUndefinedArrayOffset */
    $app->handle($_SERVER['REQUEST_URI']);
} catch (Throwable $e) {
    /** @psalm-suppress ForbiddenCode */
    var_dump($e);
    //(new Debug())->onUncaughtException($e);
}
