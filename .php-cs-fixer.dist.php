<?php
/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.12.0|configurator
 * you can change this configuration by importing this file.
 */
$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    /* @see https://github.com/kubawerlos/php-cs-fixer-custom-fixers */
    ->registerCustomFixers(new PhpCsFixerCustomFixers\Fixers())
    ->setRules([
        '@PSR12' => true,
        '@PhpCsFixer' => true,
        '@Symfony' => true,
        // Escape implicit backslashes in strings and heredocs to ease the understanding of which are special chars interpreted by PHP and which not.
        'escape_implicit_backslashes' => false,
        // Transforms imported FQCN parameters and return types in function arguments to short version.
        'fully_qualified_strict_types' => false,
        // Heredoc/nowdoc content must be properly indented. Requires PHP >= 7.3.
        'heredoc_indentation' => true,
        // Lambda must not import variables it doesn't use.
        'lambda_not_used_import' => false,
        // List (`array` destructuring) assignment should be declared using the configured syntax. Requires PHP >= 7.1.
        'list_syntax' => true,
        // DocBlocks must start with two asterisks, multiline comments must start with a single asterisk, after the opening slash. Both must end with a single asterisk before the closing slash.
        'multiline_comment_opening_closing' => false,
        // There should not be useless `else` cases.
        'no_useless_else' => false,
        // There should not be an empty `return` statement at the end of a function.
        'no_useless_return' => false,
        // PHPDoc summary should end in either a full stop, exclamation mark, or question mark.
        'phpdoc_summary' => false,
        // Converts `protected` variables and methods to `private` where possible.
        'protected_to_private' => false,
        // Local, dynamic and directly referenced variables should not be assigned and directly returned by a function or method.
        'return_assignment' => false,
        // Use `null` coalescing operator `??` where possible. Requires PHP >= 7.0.
        'ternary_to_null_coalescing' => true,
        // Write conditions in Yoda style (`true`), non-Yoda style (`['equal' => false, 'identical' => false, 'less_and_greater' => false]`) or ignore those conditions (`null`) based on configuration.
        'yoda_style' => false,
        // `@return void` and `@return null` annotations should be omitted from PHPDoc.
        'phpdoc_no_empty_return' => false,
        // Spaces should be properly placed in a function declaration.
        'function_declaration' => ['closure_fn_spacing' => 'none', 'closure_function_spacing' => 'none'],
        // Forbid multi-line whitespace before the closing semicolon or move the semicolon to the new line for chained calls.
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        // Docblocks should only be used on structural elements.
        'phpdoc_to_comment' => ['ignored_tags' => ['see']],

        // Comments must be surrounded by spaces.
        PhpCsFixerCustomFixers\Fixer\CommentSurroundedBySpacesFixer::name() => true,
        // Constructor's empty braces must be single line.
        PhpCsFixerCustomFixers\Fixer\ConstructorEmptyBracesFixer::name() => true,
        // There can be no imports from the global namespace.
        PhpCsFixerCustomFixers\Fixer\NoImportFromGlobalNamespaceFixer::name() => true,
        // Trailing comma in the list on the same line as the end of the block must be removed.
        PhpCsFixerCustomFixers\Fixer\NoTrailingCommaInSinglelineFixer::name() => true,
        // There must be no useless parentheses.
        PhpCsFixerCustomFixers\Fixer\NoUselessParenthesisFixer::name() => true,
        // The strlen or mb_strlen functions should not be compared against 0.
        PhpCsFixerCustomFixers\Fixer\NoUselessStrlenFixer::name() => true,
        // Generic array style should be used in PHPDoc.
        PhpCsFixerCustomFixers\Fixer\PhpdocArrayStyleFixer::name() => true,
        // There must be no superfluous parameters in PHPDoc.
        PhpCsFixerCustomFixers\Fixer\PhpdocNoSuperfluousParamFixer::name() => true,
        // The @param annotations must be in the same order as the function parameters.
        PhpCsFixerCustomFixers\Fixer\PhpdocParamOrderFixer::name() => true,
        // The @var annotations must be on a single line if they are the only content.
        PhpCsFixerCustomFixers\Fixer\PhpdocSingleLineVarFixer::name() => true,
        // PHPDoc types commas must not be preceded by whitespace, and must be succeeded by single whitespace.
        PhpCsFixerCustomFixers\Fixer\PhpdocTypesCommaSpacesFixer::name() => true,
        // PHPDoc types must be trimmed.
        PhpCsFixerCustomFixers\Fixer\PhpdocTypesTrimFixer::name() => true,
        // Statements not preceded by a line break must be preceded by a single space.
        PhpCsFixerCustomFixers\Fixer\SingleSpaceBeforeStatementFixer::name() => true,
        // A class that implements the __toString () method must explicitly implement the Stringable interface.
        PhpCsFixerCustomFixers\Fixer\StringableInterfaceFixer::name() => true,

        // RISKY! Replaces `intval`, `floatval`, `doubleval`, `strval` and `boolval` function calls with according type casting operator.
        'modernize_types_casting' => true,
        // RISKY! Replace multiple nested calls of `dirname` by only one call with second `$level` parameter. Requires PHP >= 7.0.
        'combine_nested_dirname' => true,
        // RISKY! Replaces `dirname(__FILE__)` expression with equivalent `__DIR__` constant.
        'dir_constant' => true,
        // RISKY! Replace deprecated `ereg` regular expression functions with `preg`.
        'ereg_to_preg' => true,
        // RISKY! Order the flags in `fopen` calls, `b` and `t` must be last.
        'fopen_flag_order' => true,
        // RISKY! The flags in `fopen` calls must omit `t`, and `b` must be omitted or included consistently.
        'fopen_flags' => true,
        // RISKY! Master functions shall be used instead of aliases.
        'no_alias_functions' => true,
        // RISKY! There must be no `sprintf` calls with only the first argument.
        'no_useless_sprintf' => true,
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        ->in([
            __DIR__.'/app',
            __DIR__.'/config',
            __DIR__.'/database',
            __DIR__.'/public',
            __DIR__.'/routes',
            __DIR__.'/tests',
        ])
        ->append([
            __DIR__.'/.php-cs-fixer.dist.php',
        ])
        ->exclude(__DIR__.'/vendor')
    );
