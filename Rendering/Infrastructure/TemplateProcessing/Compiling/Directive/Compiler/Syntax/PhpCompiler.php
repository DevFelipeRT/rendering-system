<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\Syntax;

use Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\AbstractDirectiveCompiler;

/**
 * A compiler pass that transforms raw PHP directives into standard PHP tags.
 *
 * This compiler handles two forms of the directive:
 * - @php(...) for single-line statements.
 * - @php / @endphp for multi-line blocks.
 */
final class PhpCompiler extends AbstractDirectiveCompiler
{
    /**
     * An associative array of directives that require parameters in parentheses.
     */
    protected const PARAMETERIZED_DIRECTIVES = [
        'php' => '/@php\s*\(/',
    ];

    /**
     * An associative array of parameterless directives and their direct PHP replacements.
     */
    protected const PARAMETERLESS_DIRECTIVES = [
        '/@php\b/'    => '<?php',
        '/@endphp\b/' => '?>',
    ];

    /**
     * A template for the output of php single-line directives.
     */
    protected const PARAMETERIZED_OUTPUT_TEMPLATE = '<?php {$expression}; ?>';
}