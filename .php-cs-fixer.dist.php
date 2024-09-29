<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PSR12' => true,
        'php_unit_test_class_requires_covers' => false,
        'php_unit_internal_class' => false,
        'fully_qualified_strict_types' => true,
        'php_unit_method_casing' => ['case' => 'snake_case'],
        'php_unit_strict' => false,
        'declare_strict_types' => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setIndent('    ')
    ->setLineEnding("\n");
