<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Building\Renderable\Partial\Builder;

use Rendering\Domain\Contract\Service\Building\Partial\HeaderBuilderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\HeaderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationInterface;
use Rendering\Domain\ValueObject\Renderable\Partial\Header;
use Rendering\Infrastructure\Building\Renderable\Partial\Factory\PartialFactory;
use Rendering\Infrastructure\Contract\Factory\ValueObject\RenderableDataFactoryInterface;

/**
 * Implements the Builder pattern to assemble a complete Header object.
 */
final class HeaderBuilder extends PartialBuilder implements HeaderBuilderInterface
{
    private const DEFAULT_TEMPLATE = 'partial/header.phtml';

    private ?NavigationInterface $navigation = null;

    /**
     * Constructor to initialize the HeaderBuilder with necessary factories.
     * Sets the default template file for the header.
     * Template files can be overridden by calling setTemplateFile().
     *
     * @param PartialFactory $partialFactory Factory to create partial views.
     * @param PartialFactory $partialFactory Factory to create partial views.
     * @param RenderableDataFactoryInterface $dataFactory Factory to create renderable data.
     */
    public function __construct(
        PartialFactory $partialFactory,
        RenderableDataFactoryInterface $dataFactory
    ) {
        parent::__construct($partialFactory, $dataFactory);
        $this->initializeTemplateFile(self::DEFAULT_TEMPLATE);
    }

    public function setNavigation(NavigationInterface $navigation): HeaderBuilderInterface
    {
        $this->navigation = $navigation;
        $this->isConfigured = true;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function build(): HeaderInterface
    {
        $this->checkReadyState();
        $this->updatePartials();
        return new Header(
            $this->templateFile,
            $this->buildDataFromArray($this->data),
            $this->buildPartialsCollection($this->partials),
            $this->navigation
        );
    }

    private function updatePartials(): void
    {
        if ($this->navigation !== null) {
            $this->addPartial('navigation', $this->navigation);
        }
    }
}
