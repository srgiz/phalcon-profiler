<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\View\Fn;

class DumpFn
{
    public static function execute(mixed $data): string
    {
        return (new self())($data);
    }

    public function __invoke(mixed $data): string
    {
        if (!is_iterable($data)) {
            return $this->dumpString($data);
        }

        return sprintf('<pre class="mb-0"><code>%s</code></pre>', $this->dump($data));
    }

    private function dump(iterable $data, int $lvl = 0): string
    {
        $tab = $this->tab(1);
        $offset = $this->tab($lvl);

        $str = '[';

        foreach ($data as $key => $value) {
            $str .= "\n".$offset.$tab.$this->dumpKey($key).' => '
                .(is_array($value) ? $this->dump($value, $lvl + 1) : $this->dumpString($value))
                .','
            ;
        }

        return $str."\n".$offset.']';
    }

    private function tab(int $lvl): string
    {
        $str = '';
        $limit = $lvl * 2;

        while ($limit-- > 0) {
            $str .= '&nbsp;';
        }

        return $str;
    }

    private function dumpString(mixed $str): string
    {
        $color = 'var(--bs-success-text-emphasis)';

        if (is_int($str) || is_float($str)) {
            $color = 'var(--bs-info-text-emphasis)';
        } elseif (is_bool($str) || is_null($str)) {
            $color = 'var(--bs-warning-text-emphasis)';
        }

        $quotes = is_string($str) || $str instanceof \Stringable;

        try {
            $toStr = (string) $str;
        } catch (\Throwable $e) {
            if ($str instanceof \DateTimeInterface) {
                $toStr = sprintf('%s "%s"', get_class($str), $str->format('c'));
            } else {
                $toStr = is_object($str) ? get_class($str) : gettype($str);
                $color = 'var(--bs-warning-text-emphasis)';
            }
        }

        $result = sprintf('<span style="color: %s">%s</span>', $color, htmlspecialchars($toStr));

        return $quotes ? sprintf('"%s"', $result) : $result;
    }

    private function dumpKey(mixed $key): string
    {
        return sprintf('"<span style="color: var(--bs-emphasis-color)">%s</span>"', htmlspecialchars((string) $key));
    }
}
