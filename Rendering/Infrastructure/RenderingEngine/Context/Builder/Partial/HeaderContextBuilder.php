<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Context\Builder\Partial;

use InvalidArgumentException;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\HeaderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;
use Rendering\Infrastructure\RenderingEngine\Context\Builder\AbstractContextBuilder;

/**
 * Builds the rendering context for a HeaderInterface object.
 */
final class HeaderContextBuilder extends AbstractContextBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function validateRenderable(RenderableInterface $renderable): void
    {
        if (!$renderable instanceof HeaderInterface) {
            throw new InvalidArgumentException('HeaderContextBuilder only supports HeaderInterface instances.');
        }
    }

    /**
     * {@inheritdoc}
     * @param HeaderInterface $renderable
     */
    protected function buildTemplateData(RenderableInterface $renderable): array
    {
        $data = $this->extractData($renderable);

        $data['header'] = $renderable;
        $data['navigation'] = $renderable->navigation();
        
        return $data;
    }
}