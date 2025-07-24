<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\Structural;

use Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\AbstractDirectiveCompiler;
use RuntimeException;

/**
 * Compiles the @extends directive into a PHP call to the ViewApi.
 *
 * This compiler is responsible for translating the template inheritance directive
 * into a runtime method call that will be handled by the rendering service.
 */
final class LayoutCompiler extends AbstractDirectiveCompiler
{
    /**
     * Defines the @extends directive as a parameterized directive for the parent class to parse.
     * @var array<string, string>
     */
    protected const PARAMETERIZED_DIRECTIVES = [
        'extends' => '/@extends\s*\(/',
    ];

    /**
     * Overrides the parent compile method to add validation before compilation.
     *
     * It ensures only one @extends directive exists before letting the parent
     * class handle the compilation logic.
     */
    public function compile(string $content): string
    {
        $this->validateSingleDirectiveOccurrence($content);

        return parent::compile($content);
    }

    /**
     * Provides the replacement logic for the @extends directive.
     */
    protected function buildParameterizedReplacement(string $name, string $expression): string
    {
        $layoutName = trim($expression, ' \'"');
        return "<?php \$viewApi->extend('{$layoutName}'); ?>";
    }

    /**
     * Validates that only one @extends directive is present in the content.
     *
     * @param string $content The template content.
     * @throws RuntimeException If multiple @extends directives are found.
     */
    private function validateSingleDirectiveOccurrence(string $content): void
    {
        preg_match_all('/@extends\s*\(/', $content, $matches);

        if (isset($matches[0]) && count($matches[0]) > 1) {
            throw new RuntimeException('Multiple @extends directives found. Only one is allowed per template.');
        }
    }
}