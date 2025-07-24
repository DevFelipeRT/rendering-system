<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Context\Builder\Partial;

use InvalidArgumentException;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;
use Rendering\Infrastructure\RenderingEngine\Context\Builder\AbstractContextBuilder;

/**
 * Builds the rendering context for a NavigationInterface object.
 */
final class NavigationContextBuilder extends AbstractContextBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function validateRenderable(RenderableInterface $renderable): void
    {
        if (!$renderable instanceof NavigationInterface) {
            throw new InvalidArgumentException('NavigationContextBuilder only supports NavigationInterface instances.');
        }
    }

    /**
     * {@inheritdoc}
     * @param NavigationInterface $renderable
     */
    protected function buildTemplateData(RenderableInterface $renderable): array
    {
        $data = $this->extractData($renderable);

        $data['navigation'] = $renderable;
        $data['links'] = $renderable->links();

        return $data;
    }
}