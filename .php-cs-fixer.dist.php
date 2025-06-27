<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude([
        'config',
        'example/app/var',
    ])
;

return (new PhpCsFixer\Config())
    //->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules([
        '@Symfony' => true,
        'single_line_throw' => false,
        'single_line_comment_spacing' => false,
        'declare_strict_types' => true,
        'date_time_immutable' => true, // only immutable
        'blank_line_before_statement' => [
            'statements' => [
                'return', // new line before return
            ],
        ],
        //'phpdoc_separation' => false, // new line after group params
        'phpdoc_to_comment' => [
            'ignored_tags' => [
                'var', /** @var */
                'see',
                'psalm-suppress',
                //'phpstan-ignore',
            ],
        ],
        'phpdoc_align' => false,
    ])
    ->setFinder($finder)
    ->setCacheFile('.php-cs-fixer.cache')
    ->setLineEnding("\n")
    ->setRiskyAllowed(true) // for declare_strict_types | date_time_immutable
;
