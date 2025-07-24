<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\Control;

use Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\AbstractDirectiveCompiler;

/**
 * A compiler pass that transforms loop directives into PHP.
 *
 * This compiler handles @foreach, @forelse, @for, @while, and other
 * loop-related statements by extending the AbstractDirectiveCompiler.
 */
final class LoopCompiler extends AbstractDirectiveCompiler
{
    /**
     * An associative array of directives that are followed by a parenthesized expression.
     */
    protected const PARAMETERIZED_DIRECTIVES = [
        'foreach' => '/@foreach\s*\(/',
        'forelse' => '/@forelse\s*\(/',
        'for'     => '/@for\s*\(/',
        'while'   => '/@while\s*\(/',
    ];

    /**
     * An associative array of directives that do not require parameters.
     */
    protected const PARAMETERLESS_DIRECTIVES = [
        '/@endforeach\b/' => '<?php endforeach; ?>',
        '/@empty\b/'      => '<?php endforeach; else: ?>',
        '/@endforelse\b/' => '<?php endif; ?>',
        '/@endfor\b/'      => '<?php endfor; ?>',
        '/@endwhile\b/'   => '<?php endwhile; ?>',
        '/@break\b/'      => '<?php break; ?>',
        '/@continue\b/'   => '<?php continue; ?>',
    ];

    /**
     * A template for the output of loop directives.
     */
    protected const OUTPUT_TEMPLATE = '<?php if(!empty({$iterable}) && (is_array({$iterable}) || {$iterable} instanceof \Countable)): foreach({$expression}): ?>';

    /**
     * {@inheritdoc}
     */
    protected function buildParameterizedReplacement(string $name, string $expression): string
    {
        if ($name === 'forelse') {
            return $this->compileForelse($expression);
        }
        return parent::buildParameterizedReplacement($name, $expression);
    }

    /**
     * A custom handler for the @forelse directive's specific logic.
     * 
     * @param string $expression The expression within the @forelse directive.
     * @return string The PHP code that replaces the @forelse directive.
     */
    private function compileForelse(string $expression): string
    {
        $iterable = trim(explode(' as ', $expression)[0]);
        return str_replace(
            ['{$iterable}', '{$expression}'],
            [$iterable, str_replace(' as ', ' => ', $expression)],
            static::OUTPUT_TEMPLATE
        );
    }
}
