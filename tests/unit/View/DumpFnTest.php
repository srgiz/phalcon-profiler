<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Tests\View;

use PHPUnit\Framework\TestCase;
use Srgiz\Phalcon\WebProfiler\View\Fn\DumpFn;

class DumpFnTest extends TestCase
{
    public function examples(): array
    {
        return [
            ['str', '"<span style="color: var(--bs-success-text-emphasis)">str</span>"'],
            [123, '<span style="color: var(--bs-info-text-emphasis)">123</span>'],
            [null, '<span style="color: var(--bs-warning-text-emphasis)">null</span>'],
            [false, '<span style="color: var(--bs-warning-text-emphasis)">false</span>'],
            [new \DateTimeImmutable('2025-07-07T10:10:40+03:00'), '<span style="color: var(--bs-success-text-emphasis)">DateTimeImmutable &quot;2025-07-07T10:10:40+03:00&quot;</span>'],
            [new ObjDummy(), '<span style="color: var(--bs-warning-text-emphasis)">Srgiz\Phalcon\WebProfiler\Tests\View\ObjDummy</span>'],
            [
                [
                    'foo' => 'bar',
                    'bar' => null,
                    'items' => [
                        ['obj' => new \DateTimeImmutable('2025-07-07T08:10:35+03:00')],
                        ['obj' => new ObjDummy()],
                    ],
                ],
                '<pre class="mb-0"><code>[
&nbsp;&nbsp;"<span style="color: var(--bs-emphasis-color)">foo</span>" => "<span style="color: var(--bs-success-text-emphasis)">bar</span>",
&nbsp;&nbsp;"<span style="color: var(--bs-emphasis-color)">bar</span>" => <span style="color: var(--bs-warning-text-emphasis)">null</span>,
&nbsp;&nbsp;"<span style="color: var(--bs-emphasis-color)">items</span>" => [
&nbsp;&nbsp;&nbsp;&nbsp;"<span style="color: var(--bs-emphasis-color)">0</span>" => [
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"<span style="color: var(--bs-emphasis-color)">obj</span>" => <span style="color: var(--bs-success-text-emphasis)">DateTimeImmutable &quot;2025-07-07T08:10:35+03:00&quot;</span>,
&nbsp;&nbsp;&nbsp;&nbsp;],
&nbsp;&nbsp;&nbsp;&nbsp;"<span style="color: var(--bs-emphasis-color)">1</span>" => [
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"<span style="color: var(--bs-emphasis-color)">obj</span>" => <span style="color: var(--bs-warning-text-emphasis)">Srgiz\Phalcon\WebProfiler\Tests\View\ObjDummy</span>,
&nbsp;&nbsp;&nbsp;&nbsp;],
&nbsp;&nbsp;],
]</code></pre>',
            ],
        ];
    }

    /**
     * @dataProvider examples
     */
    public function testDumpFn(mixed $value, string $expected): void
    {
        $this->assertSame($expected, DumpFn::execute($value));
    }
}

class ObjDummy
{
    public $foo = 'bar';
}
