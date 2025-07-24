<?php


declare(strict_types=1);

namespace Rendering\Domain\Contract\Service\Building\Partial;

use Rendering\Domain\Contract\Service\Building\RenderableBuilderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialViewInterface;
use Rendering\Domain\Contract\Service\Building\Exception\BuilderExceptionInterface;

/**
 * Defines the contract for a Partial component builder.
 * 
 * This interface extends the RenderableBuilderInterface and
 * provides enhanced capabilities to build partial components
 * using various input formats.
 */
interface PartialBuilderInterface extends RenderableBuilderInterface
{
    /**
     * Sets partials from raw data or objects.
     * 
     * Accepts both formats:
     * - ['name' => PartialViewInterface] (pre-built objects)
     * - ['name' => ['template' => 'path', 'data' => [...], 'partials' => [...]]] (raw data)
     * - ['name' => 'template-path.twig'] (template path string)
     *
     * @param array $partials Mixed array of partial objects or definition arrays.
     * @return self
     */
    public function setPartials(array $partials): self;

    /**
     * Adds a single partial from raw data or object.
     * 
     * This method overrides the parent method to provide enhanced capabilities.
     * It accepts the following formats:
     * - PartialViewInterface (pre-built object, compatible with parent)
     * - string (template path only)
     * - array (full definition: ['template' => 'path', 'data' => [...], 'partials' => [...]])
     *
     * @param string $key The identifier for the partial.
     * @param mixed $partial The partial object or data definition.
     * @return self
     */
    public function addPartial(string $key, mixed $partial): self;

    /**
     * Builds and returns a partial view instance.
     * 
     * @return PartialViewInterface The constructed partial view.
     * @throws BuilderExceptionInterface If the builder is not in a valid state.
     */
    public function build(): PartialViewInterface;
    
    /**
     * Creates a generic PartialView using the current builder state.
     * 
     * This method ensures that specialized builders can always create
     * the base type, enabling type-safe polymorphic usage.
     *
     * @return PartialViewInterface The constructed generic partial view.
     * @throws BuilderExceptionInterface If the builder is not in a valid state.
     */
    public function createPartialView(): PartialViewInterface;
}