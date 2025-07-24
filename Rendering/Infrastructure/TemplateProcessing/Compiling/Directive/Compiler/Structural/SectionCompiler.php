<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\Structural;

use Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\AbstractDirectiveCompiler;

/**
 * Compiles @section directives by leveraging the AbstractDirectiveCompiler.
 *
 * This compiler defines patterns for parameterized (@section) and parameterless
 * (@endsection) directives and provides the logic to translate them into the
 * appropriate ViewApi method calls.
 */
final class SectionCompiler extends AbstractDirectiveCompiler
{
    /**
     * Defines the parameterized @section directive for the parent class to parse.
     * @var array<string, string>
     */
    protected const PARAMETERIZED_DIRECTIVES = [
        'section' => '/@section\s*\(/',
    ];

    /**
     * Defines the parameterless @endsection directive and its direct replacement.
     * @var array<string, string>
     */
    protected const PARAMETERLESS_DIRECTIVES = [
        '/@endsection/' => '<?php $viewApi->stopSection(); ?>',
    ];

    /**
     * Provides the custom replacement logic for the @section directive.
     *
     * This method is called by the parent's `compileParameterizedDirectives` loop
     * and handles both value-based and block-based section openings by delegating
     * to specialized private methods.
     */
    protected function buildParameterizedReplacement(string $name, string $expression): string
    {
        $parts = str_getcsv($expression, ',', "'");
        $sectionName = trim($parts[0]);
        $sectionValue = isset($parts[1]) ? trim($parts[1]) : null;

        if ($sectionValue !== null) {
            return $this->compileValueBasedSection($sectionName, $sectionValue);
        }

        return $this->compileBlockOpening($sectionName);
    }

    /**
     * Builds the complete PHP block for a value-based section.
     *
     * @param string $sectionName The name of the section.
     * @param string $sectionValue The value to be rendered inside the section.
     * @return string The compiled PHP code.
     */
    private function compileValueBasedSection(string $sectionName, string $sectionValue): string
    {
        return "<?php \$viewApi->startSection('{$sectionName}'); ?>"
             . "<?= {$sectionValue} ?>"
             . "<?php \$viewApi->stopSection(); ?>";
    }

    /**
     * Builds the PHP code for a block-opening section tag.
     *
     * @param string $sectionName The name of the section.
     * @return string The compiled PHP code.
     */
    private function compileBlockOpening(string $sectionName): string
    {
        return "<?php \$viewApi->startSection('{$sectionName}'); ?>";
    }
}