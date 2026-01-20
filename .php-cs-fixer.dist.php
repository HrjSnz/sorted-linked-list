<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

return new Config()
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@PHP84Migration' => true,
        'blank_line_before_statement' => [
            'statements' => ['return'],
        ],
        'no_unused_imports' => true,
    ])
    ->setFinder(
        new Finder()
            ->in(__DIR__)
            ->exclude(['vendor', 'var', 'build'])
    );
