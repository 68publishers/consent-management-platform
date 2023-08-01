<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixerCustomFixers\Fixers as CustomFixers;
use PhpCsFixerCustomFixers\Fixer\ConstructorEmptyBracesFixer;
use PhpCsFixerCustomFixers\Fixer\MultilinePromotedPropertiesFixer;
use PhpCsFixerCustomFixers\Fixer\PromotedConstructorPropertyFixer;

$finder = Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/public')
    ->in(__DIR__ . '/tests');

return (new Config())
    ->registerCustomFixers(new CustomFixers())
    ->setUsingCache(false)
    ->setIndent("    ")
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => [
            'syntax' => 'short'
        ],
        'trailing_comma_in_multiline' => [
            'elements' => [
                'arguments',
                'arrays',
                'match',
                'parameters',
            ],
        ],
        'constant_case' => [
            'case' => 'lower',
        ],
        'declare_strict_types' => true,
        'phpdoc_align' => true,
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue',
                'declare',
                'return'
            ],
        ],
        'blank_line_after_namespace' => true,
        'single_blank_line_before_namespace' => true,
        'return_type_declaration' => [
            'space_before' => 'none',
        ],
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => [
                'class',
                'function',
                'const'
            ],
        ],
        'no_unused_imports' => true,
        'single_line_after_imports' => true,
        'no_leading_import_slash' => true,
        'global_namespace_import' => [
            'import_constants' => true,
            'import_functions' => true,
            'import_classes' => true,
        ],
        'fully_qualified_strict_types' => true,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => false,
            'remove_inheritdoc' => true,
            'allow_unused_params' => false,
        ],
        'no_empty_phpdoc' => true,
        'no_blank_lines_after_phpdoc' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_trim' => true,
        'no_extra_blank_lines' => [
            'tokens' => [
                'curly_brace_block',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'throw',
                'use',
            ],
        ],
        'single_trait_insert_per_statement' => true,
        'single_class_element_per_statement' => [
            'elements' => [
                'const',
                'property',
            ]
        ],
        'function_typehint_space' => true,
        ConstructorEmptyBracesFixer::name() => true,
        MultilinePromotedPropertiesFixer::name() => true,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
