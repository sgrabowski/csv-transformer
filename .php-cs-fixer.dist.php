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
        '@PSR12:risky' => true,
        '@PHP80Migration' => true,
        '@PHP80Migration:risky' => true,
        '@PHP82Migration' => true,
        '@PHPUnit84Migration:risky' => true,
        'align_multiline_comment' => ['comment_type' => 'all_multiline'],
        'assign_null_coalescing_to_coalesce_equal' => false,
        'attribute_empty_parentheses' => true,
        'blank_line_before_statement' => true,
        'blank_line_between_import_groups' => false,
        'class_attributes_separation' => ['elements' => ['const' => 'none', 'method' => 'one', 'property' => 'none', 'trait_import' => 'only_if_meta', 'case' => 'only_if_meta']],
        'class_keyword' => true,
        'comment_to_phpdoc' => false,
        'concat_space' => ['spacing' => 'one'],
        'final_internal_class' => true,
        'fopen_flags' => true,
        'fully_qualified_strict_types' => true,
        'increment_style' => ['style' => 'post'],
        'mb_str_functions' => false,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'native_function_invocation' => ['include' => ['@all']],
        'no_alias_functions' => true,
        'no_null_property_initialization' => false, // Required for some nullable typed properties.
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true],
        'no_unneeded_braces' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unreachable_default_argument_value' => false,
        'no_unset_on_property' => false,
        'no_whitespace_before_comma_in_array' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha', 'imports_order' => ['class', 'function', 'const']],
        'php_unit_data_provider_name' => false,
        'php_unit_data_provider_return_type' => true,
        'php_unit_internal_class' => false,
        'php_unit_method_casing' => ['case' => 'snake_case'],
        'php_unit_strict' => false,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
        'php_unit_test_class_requires_covers' => false,
        'phpdoc_param_order' => true,
        'phpdoc_readonly_class_comment_to_keyword' => true,
        'phpdoc_to_comment' => false, // Causes issues with PHPStan's parsing of types.
        'phpdoc_to_param_type' => true,
        'phpdoc_to_property_type' => true,
        'phpdoc_to_return_type' => true,
        'regular_callable_call' => true,
        'simplified_if_return' => true,
        'simplified_null_return' => true,
        'single_line_comment_style' => false,
        'single_line_throw' => false,
        'single_quote' => true,
        'space_after_semicolon' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters', 'match']],
        'types_spaces' => ['space' => 'single'],
        'unary_operator_spaces' => true,
        'use_arrow_functions' => false,
        'whitespace_after_comma_in_array' => true,
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false, 'always_move_variable' => false],
        'method_chaining_indentation' => false,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setIndent('    ')
    ->setLineEnding("\n");
