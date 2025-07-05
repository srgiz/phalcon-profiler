<?php

declare(strict_types=1);

$clover = __DIR__.'/../files/.phpunit.cache/coverage/clover.xml';

if (!file_exists($clover)) {
    throw new Exception('File not found');
}

$reader = new XMLReader();
$reader->open($clover);
$statements = 1;
$coveredStatements = 0;

while ($reader->read()) {
    if (XMLReader::ELEMENT === $reader->nodeType && 'metrics' === $reader->name && 2 === $reader->depth) {
        $statements = (int) $reader->getAttribute('statements');
        $coveredStatements = (int) $reader->getAttribute('coveredstatements');
        break;
    }
}

$percent = round(100 / $statements * $coveredStatements);

$color = '#a4a61d';      // Default Gray
if ($percent < 40) {
    $color = '#e05d44';  // Red
} elseif ($percent < 60) {
    $color = '#fe7d37';  // Orange
} elseif ($percent < 75) {
    $color = '#dfb317';  // Yellow
} elseif ($percent < 90) {
    $color = '#a4a61d';  // Yellow-Green
} elseif ($percent < 95) {
    $color = '#97CA00';  // Green
} else {
    $color = '#4c1';     // Bright Green
}

$svg = file_get_contents(__DIR__.'/tpl.svg');
file_put_contents(__DIR__.'/coverage.svg', str_replace(['$color$', '$cov$'], [$color, $percent.' %'], $svg));
