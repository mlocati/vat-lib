<?php
/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.51.0|configurator
 * you can change this configuration by importing this file.
 */
$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS' => true,
        '@PER-CS:risky' => true,
        // PHP arrays should be declared using the configured syntax.
        'array_syntax' => true,
        // Ordering `use` statements.
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        // Visibility MUST be declared on all properties and methods; `abstract` and `final` MUST be declared before the visibility; `static` MUST be declared after the visibility.
        'visibility_required' => ['elements' => ['method', 'property']],
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        ->in(__DIR__)
    )
;
