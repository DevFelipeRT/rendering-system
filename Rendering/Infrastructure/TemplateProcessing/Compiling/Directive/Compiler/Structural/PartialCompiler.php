<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\Structural;

use Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\AbstractDirectiveCompiler;

/**
 * A compiler pass that transforms @partial directives into PHP render calls.
 *
 * This compiler intelligently handles variables and expressions for the
 * @partial directive, which accepts a single argument for the partial name.
 */
final class PartialCompiler extends AbstractDirectiveCompiler
{
    /**
     * Registers @partial as a directive that requires a parenthesized expression.
     */
    protected const PARAMETERIZED_DIRECTIVES = [
        'partial' => '/@partial\s*\(/',
    ];

    /**
     * A template for the output of partial directives.
     * The %s placeholder is for the partial name/path.
     */
    protected const OUTPUT_TEMPLATE = '<?= $viewApi->renderPartial(%s) ?>';

    /**
     * {@inheritdoc}
     *
     * Provides the specific replacement logic for the @partial directive,
     * using the entire expression from within the parentheses as the partial's name.
     */
    protected function buildParameterizedReplacement(string $name, string $expression): string
    {
        // Since this directive only accepts one argument, the entire expression
        // within the parentheses is treated as the name of the partial to render.
        return sprintf(static::OUTPUT_TEMPLATE, $expression);
    }
}