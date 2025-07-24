<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\Structural;

use Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\AbstractDirectiveCompiler;

/**
 * Compiles the @yield directive into a PHP call to the ViewApi.
 *
 * This compiler translates the @yield('sectionName') directive into a runtime
 * method call, which will be handled by the rendering service's presentation stage.
 */
final class YieldCompiler extends AbstractDirectiveCompiler
{
    /**
     * Defines the @yield directive as a parameterized directive for the parent class to parse.
     * @var array<string, string>
     */
    protected const PARAMETERIZED_DIRECTIVES = [
        'yield' => '/@yield\s*\(/',
    ];

    /**
     * Provides the replacement logic for the @yield directive.
     *
     * This method is called by the parent's `compileParameterizedDirectives` loop.
     */
    protected function buildParameterizedReplacement(string $name, string $expression): string
    {
        $sectionName = trim($expression, ' \'"');
        return "<?= \$viewApi->yieldSection('{$sectionName}'); ?>";
    }
}