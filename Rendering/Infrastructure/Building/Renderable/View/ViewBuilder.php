<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Building\Renderable\View;

use Rendering\Domain\Contract\Service\Building\View\ViewBuilderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\View\ViewInterface;
use Rendering\Domain\ValueObject\Renderable\View\View;
use Rendering\Infrastructure\Building\Renderable\AbstractRenderableBuilder;
use Rendering\Infrastructure\Building\Renderable\Partial\Factory\PartialFactory;
use Rendering\Infrastructure\Building\Renderable\Trait\BuilderPartialHandlingTrait;
use Rendering\Infrastructure\Contract\Factory\ValueObject\RenderableDataFactoryInterface;

/**
 * ViewBuilder is responsible for constructing a View object with a specific template,
 * data, and partials. It extends the AbstractRenderableBuilder to leverage common
 * functionality for rendering.
 */
class ViewBuilder extends AbstractRenderableBuilder implements ViewBuilderInterface
{
    use BuilderPartialHandlingTrait;

    private string $title = '';
    private string $description = '';

    /**
     * Constructs a new ViewBuilder instance.
     *
     * @param PartialFactory $partialFactory The factory for creating partials.
     * @param RenderableDataFactoryInterface $renderableDataFactory The factory for renderable data.
     */
    public function __construct(
        PartialFactory $partialFactory,
        RenderableDataFactoryInterface $renderableDataFactory
    ) {
        $this->partialFactory = $partialFactory;
        parent::__construct($renderableDataFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        $this->isConfigured = true;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        $this->isConfigured = true;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function build(): ViewInterface
    {
        return new View(
            $this->templateFile, 
            $this->buildDataFromArray($this->data), 
            $this->buildPartialsCollection($this->partials),
            $this->title ?? null
        );
    }
}
