<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Tests;

use Phalcon\Config\Config;
use Phalcon\Di\Di;
use Phalcon\Di\DiInterface;
use Srgiz\Phalcon\WebProfiler\WebProfiler;

trait PhalconTestCaseTrait
{
    protected DiInterface $di;

    protected function setUpDi(array $config, array $services): void
    {
        //Di::reset();
        $this->di = new Di();
        $this->di->setShared('config', new Config($config));

        foreach ($services as $name => $service) {
            $this->di->set($name, $service, isset($service['shared']) && $service['shared']);
        }

        $this->di->register(new WebProfiler());
    }
}
