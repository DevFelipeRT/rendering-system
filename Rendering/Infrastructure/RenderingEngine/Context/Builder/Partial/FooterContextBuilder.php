<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Context\Builder\Partial;

use InvalidArgumentException;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\FooterInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;
use Rendering\Infrastructure\RenderingEngine\Context\Builder\AbstractContextBuilder;

/**
 * Builds the rendering context for a FooterInterface object.
 */
final class FooterContextBuilder extends AbstractContextBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function validateRenderable(RenderableInterface $renderable): void
    {
        if (!$renderable instanceof FooterInterface) {
            throw new InvalidArgumentException('FooterContextBuilder only supports FooterInterface instances.');
        }
    }

    /**
     * {@inheritdoc}
     * @param FooterInterface $renderable
     */
    protected function buildTemplateData(RenderableInterface $renderable): array
    {
        $data = $this->extractData($renderable);

        $data['footer'] = $renderable;
        $data['copyright'] = $renderable->copyright();

        return $data;
    }
}