<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Context\Builder;

use InvalidArgumentException;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\Context\ContextBuilderInterface;
use Rendering\Infrastructure\RenderingEngine\Context\Dto\ApiContext;
use Rendering\Infrastructure\RenderingEngine\Context\Dto\RenderContext;

/**
 * Abstract base class for specialized context builders.
 *
 * Provides common functionality for building rendering contexts from renderable
 * objects. Subclasses must implement specific data building logic and type
 * validation. This class produces both RenderContext and ApiContext in a single
 * operation to ensure efficiency.
 */
abstract class AbstractContextBuilder implements ContextBuilderInterface
{
    /**
     * Builds the complete context for a renderable object.
     *
     * This method orchestrates the creation of both the RenderContext (for template
     * data) and the ApiContext (for the ViewApi), returning them as an array.
     * This ensures that validation and data extraction logic is run only once.
     *
     * @param RenderableInterface $renderable The object to build the context for.
     * @return array{0: RenderContext, 1: ApiContext} A tuple containing the two context objects.
     */
    public final function build(RenderableInterface $renderable): array
    {
        $this->validateRenderable($renderable);
        
        $templateData = $this->buildTemplateData($renderable);
        $apiContextObjects = $this->buildApiContext($renderable);

        return [
            new RenderContext($templateData),
            new ApiContext($apiContextObjects)
        ];
    }

    /**
     * Validates that the renderable is of the expected type.
     *
     * @param RenderableInterface $renderable
     * @throws InvalidArgumentException if the type is not supported
     */
    abstract protected function validateRenderable(RenderableInterface $renderable): void;

    /**
     * Builds the template data array for the renderable.
     *
     * @param RenderableInterface $renderable
     * @return array<string, mixed> The template data
     */
    abstract protected function buildTemplateData(RenderableInterface $renderable): array;

    /**
     * Builds the API context for the renderable.
     *
     * The default implementation returns an array containing the renderable itself.
     * Subclasses can override this method to provide different behavior.
     *
     * @param RenderableInterface $renderable
     * @return array The API context objects
     */
    protected function buildApiContext(RenderableInterface $renderable): array
    {
        return [$renderable];
    }

    /**
     * Helper method to extract data from a renderable's data object.
     *
     * @param RenderableInterface $renderable
     * @return array<string, mixed> The data array, or an empty array if there is no data.
     */
    protected function extractData(RenderableInterface $renderable): array
    {
        return $renderable->data()?->all() ?? [];
    }
}