<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Context\Builder\Partial;

use Rendering\Infrastructure\RenderingEngine\Context\Builder\AbstractContextBuilder;
use InvalidArgumentException;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialViewInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;

/**
 * A specialized context builder for Partial objects.
 * 
 * Creates rendering contexts for partials by:
 * - Using the partial's data
 * - Adding the partial object for template access
 */
final class PartialContextBuilder extends AbstractContextBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function validateRenderable(RenderableInterface $renderable): void
    {
        if (!$renderable instanceof PartialViewInterface) {
            throw new InvalidArgumentException('PartialContextBuilder only supports PartialViewInterface instances.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function buildTemplateData(RenderableInterface $renderable): array
    {
        $data = $this->extractData($renderable);

        $data['partial'] = $renderable;

        return $data;
    }
}
