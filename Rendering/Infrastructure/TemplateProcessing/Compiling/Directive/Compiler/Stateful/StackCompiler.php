<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\Stateful;

use InvalidArgumentException;
use Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\AbstractDirectiveCompiler;

/**
 * A specialist compiler pass that transforms stack-related directives (@push, @endpush, @stack).
 *
 * This compiler follows a strict approach, handling only the directives it is
 * designed for and throwing an exception for any other parameterized directive.
 * This ensures a clean separation of concerns within the compiling system.
 */
final class StackCompiler extends AbstractDirectiveCompiler
{
    /**
     * Directives that require parameters in parentheses.
     */
    protected const PARAMETERIZED_DIRECTIVES = [
        'push'  => '/@push\s*\(/',
        'stack' => '/@stack\s*\(/',
    ];

    /**
     * Directives that do not have parameters.
     */
    protected const PARAMETERLESS_DIRECTIVES = [
        '/@endpush\b/' => '<?php $viewApi->stopPush(); ?>',
    ];

    /**
     * {@inheritdoc}
     *
     * Provides the specific replacement logic for @push and @stack directives.
     * Throws an exception if it encounters an unsupported parameterized directive.
     *
     * @param string $name The name of the directive (e.g., 'push').
     * @param string $expression The expression within the parentheses.
     * @return string The compiled PHP code.
     * @throws InvalidArgumentException When an unsupported directive is passed.
     */
    protected function buildParameterizedReplacement(string $name, string $expression): string
    {
        return match ($name) {
            'push'  => "<?php \$viewApi->startPush({$expression}); ?>",
            'stack' => "<?= \$viewApi->renderStack({$expression}) ?>",
            default => $this->handleUnsupportedDirective($name)
        };
    }

    /**
     * Handles unsupported directives by returning an empty string or throwing an exception.
     */
    private function handleUnsupportedDirective(string $name): string
    {
        if ($this->debug) {
            throw new InvalidArgumentException(
                "StackCompiler does not support '@{$name}'."
            );
        }
        return '';
    }
}