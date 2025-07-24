<?php

declare(strict_types=1);

namespace Rendering\Domain\ValueObject\Renderable\Partial;

use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialsCollectionInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialViewInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableDataInterface;
use Rendering\Domain\Trait\ValueObject\Renderable\PartialProviderTrait;
use Rendering\Domain\ValueObject\Renderable\Renderable;

/**
 * A generic, immutable Value Object for rendering any reusable template fragment.
 *
 * This class is used for partials that do not have a dedicated, specific VO.
 * It encapsulates its template, data, and can also contain its own nested
 * partial sub-components.
 */
class PartialView extends Renderable implements PartialViewInterface
{
 use PartialProviderTrait;

    /**
     * Constructs a new PartialView instance.
     *
     * @param string $templateFile The path to the template file for this partial view.
     * @param RenderableDataInterface|null $dataProvider Optional data provider for dynamic content.
     * @param PartialsCollectionInterface|null $partials Optional collection of partials to include in the view.
     */
    public function __construct(
        string $templateFile,
        ?RenderableDataInterface $dataProvider = null,
        ?PartialsCollectionInterface $partials = null
    ) {
        $this->initializePartials($partials);
        parent::__construct($templateFile, $dataProvider);
    }
}