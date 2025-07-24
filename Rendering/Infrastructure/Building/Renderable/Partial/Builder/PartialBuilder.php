<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Building\Renderable\Partial\Builder;

use Rendering\Domain\Contract\Service\Building\Partial\PartialBuilderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialViewInterface;
use Rendering\Domain\ValueObject\Renderable\Partial\PartialView;
use Rendering\Infrastructure\Building\Renderable\AbstractRenderableBuilder;
use Rendering\Infrastructure\Building\Renderable\Partial\Factory\PartialFactory;
use Rendering\Infrastructure\Building\Renderable\Trait\BuilderPartialHandlingTrait;
use Rendering\Infrastructure\Contract\Factory\ValueObject\RenderableDataFactoryInterface;

class PartialBuilder extends AbstractRenderableBuilder implements PartialBuilderInterface
{
    use BuilderPartialHandlingTrait;
    
    /**
     * Initializes the PartialBuilder with the necessary factories.
     * This builder allows setting a template file for the partial view,
     * which can be overridden by calling setTemplateFile().
     *
     * @param PartialFactory $partialFactory Factory to create partial views.
     * @param RenderableDataFactoryInterface $dataFactory Factory to create renderable data.
     */
    public function __construct(
        PartialFactory $partialFactory,
        RenderableDataFactoryInterface $dataFactory
    ) {
        $this->partialFactory = $partialFactory;
        parent::__construct($dataFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function build(): PartialViewInterface
    {
        return $this->partialFactory->createPartial(
            $this->templateFile, 
            $this->$this->data, 
            $this->partials
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createPartialView(): PartialViewInterface
    {
        return $this->build();
    }
}