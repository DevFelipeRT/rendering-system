<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Context\Builder;

use InvalidArgumentException;
use Rendering\Domain\Contract\ValueObject\Renderable\View\ViewInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;

final class ViewContextBuilder extends AbstractContextBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function validateRenderable(RenderableInterface $renderable): void
    {
        if (!$renderable instanceof ViewInterface) {
            throw new InvalidArgumentException('ViewContextBuilder only supports ViewInterface instances.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function buildTemplateData(RenderableInterface $renderable): array
    {
        /** @var ViewInterface $renderable */
        $data = $this->extractData($renderable);

        $data['view'] = $renderable;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildApiContext(RenderableInterface $renderable): array
    {
        /** @var ViewInterface $renderable */
        return [$renderable];
    }
}
