<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Renderer;

use Rendering\Domain\Contract\ValueObject\Renderable\Page\PageInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;

/**
 * A specialized renderer for objects that implement PageInterface.
 *
 * This class leverages the common rendering logic from AbstractRenderer
 * and specifies that the actual template to be rendered is the one associated
 * with the Page's View object.
 */
final class PageRenderer extends AbstractRenderer
{
    /**
     * {@inheritdoc}
     *
     * For a Page, the actual template to render is its associated View. The context,
     * however, is built from the Page itself to include all necessary data and partials.
     */
    protected function getTemplateTarget(RenderableInterface $renderable): string
    {
        if ($renderable instanceof PageInterface) {
            return $renderable->view()->fileName();
        }
        
        return $renderable->fileName();
    }
}
