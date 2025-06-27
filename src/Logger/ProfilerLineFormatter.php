<?php

declare(strict_types=1);

namespace Srgiz\Phalcon\WebProfiler\Logger;

use Phalcon\Logger\Formatter\Line;
use Phalcon\Logger\Item;

class ProfilerLineFormatter extends Line
{
    public function format(Item $item): string
    {
        $message = strtr(htmlspecialchars($this->format), [
            '%date%' => sprintf('<span style="color: var(--bs-emphasis-color)">%s</span>', $this->getFormattedDate($item)),
            '%level%' => sprintf('<span style="color: var(--bs-emphasis-color)">%s</span>', $item->getLevelName()),
            '%message%' => sprintf('<span style="color: var(--bs-emphasis-color)">%s</span>', htmlspecialchars($item->getMessage())),
        ]);

        $replace = [];

        foreach ($item->getContext() as $key => $value) {
            $isString = is_scalar($value) || is_null($value) || $value instanceof \Stringable;

            if (!$isString) {
                continue;
            }

            $replace['%'.$key.'%'] = sprintf('<span style="font-weight: 600">%s</span>', htmlspecialchars((string) $value));
        }

        return strtr($message, $replace);
    }
}
