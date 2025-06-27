<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Service;

class Version extends \Phalcon\Support\Version
{
    public function php(): string
    {
        return phpversion();
    }

    public function opcache(): bool
    {
        if (!function_exists('opcache_get_status')) {
            return false;
        }

        $data = @opcache_get_status();

        if (!is_array($data)) {
            return false;
        }

        return $data['opcache_enabled'] ?? false;
    }

    public function extension(string $extension): ?string
    {
        if (false !== ($version = phpversion($extension))) {
            return $version;
        }

        return null;
    }
}
