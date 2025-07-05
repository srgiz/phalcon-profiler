<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Tests\Service;

use PHPUnit\Framework\TestCase;
use Srgiz\Phalcon\WebProfiler\Service\Version;

class VersionTest extends TestCase
{
    public function extensions(): array
    {
        return [
            ['xdebug', '3.4.1'],
            ['unknown', null],
        ];
    }

    /**
     * @dataProvider extensions
     */
    public function testExtension(string $extension, ?string $ver): void
    {
        $version = new Version();
        $this->assertSame($ver, $version->extension($extension));
    }
}
