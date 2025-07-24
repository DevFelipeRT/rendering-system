<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Context\Builder;

use InvalidArgumentException;
use Rendering\Domain\Contract\ValueObject\Renderable\Page\PageInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;

/**
 * A specialized context builder for Page objects.
 * 
 * Creates rendering contexts for pages by:
 * - Merging data from page and view
 * - Using the page itself as the API context
 */
final class PageContextBuilder extends AbstractContextBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function validateRenderable(RenderableInterface $renderable): void
    {
        if (!$renderable instanceof PageInterface) {
            throw new InvalidArgumentException('PageContextBuilder only supports PageInterface instances.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function buildTemplateData(RenderableInterface $renderable): array
    {
        /** @var PageInterface $renderable */
        $pageData = $this->extractData($renderable);
        $viewData = $this->extractData($renderable->view());
        
        $data = array_merge($pageData, $viewData);
        
        $data['page'] = $renderable;
        $data['view'] = $renderable->view();

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildApiContext(RenderableInterface $renderable): array
    {
        /** @var PageInterface $renderable */
        return [$renderable, $renderable->view()];
    }
}
