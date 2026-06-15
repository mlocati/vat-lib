<?php

/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.95.7|configurator
 * you can change this configuration by importing this file.
 */

if (!defined('PhpCsFixer\\Console\\Application::VERSION')) {
    fwrite(STDERR, "Your version of PHP CS Fixer is too old: please upgrade it\n");
    exit(1);
}
if (version_compare(PhpCsFixer\Console\Application::VERSION, '3.95') < 0) {
    fprintf(STDERR, "Your version of PHP CS Fixer (%s) is too old: please upgrade it\n", PhpCsFixer\Console\Application::VERSION);
    exit(1);
}

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS' => true,
        '@PER-CS:risky' => true,
        // PHP arrays should be declared using the configured syntax.
        'array_syntax' => true,
        // Classes, constants, properties, and methods MUST have visibility declared, and keyword modifiers MUST be in the following order: inheritance modifier (`abstract` or `final`), visibility modifier (`public`, `protected`, or `private`), set-visibility modifier (`public(set)`, `protected(set)`, or `private(set)`), scope modifier (`static`), mutation modifier (`readonly`), type declaration, name.
        'modifier_keywords' => ['elements' => ['method', 'property']],
        // Ordering `use` statements.
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        // Arguments lists, array destructuring lists, arrays that are multi-line, `match`-lines and parameters lists must have a trailing comma.
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
    ])
    ->setFinder(
        (new PhpCsFixer\Finder())
        ->in(__DIR__)
        ->append([
            __FILE__,
        ]),
    )
;
