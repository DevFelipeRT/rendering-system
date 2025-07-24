<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Building\Renderable\Partial\Builder;

use Rendering\Domain\Contract\Service\Building\Partial\NavigationBuilderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationLinkCollectionInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationLinkInterface;
use Rendering\Domain\ValueObject\Renderable\Partial\Navigation\Navigation;
use Rendering\Infrastructure\Building\Renderable\Partial\Factory\NavigationLinkFactory;
use Rendering\Infrastructure\Building\Renderable\Partial\Factory\PartialFactory;
use Rendering\Infrastructure\Contract\Factory\ValueObject\RenderableDataFactoryInterface;

/**
 * Implements the Builder pattern to assemble a complete Navigation object.
 */
final class NavigationBuilder extends PartialBuilder implements NavigationBuilderInterface
{
    private const DEFAULT_TEMPLATE = 'partial/navigation.phtml';
    private NavigationLinkFactory $linkFactory;

    /**
     * @var array<NavigationLinkInterface|array>
     */
    private array $links = [];

    public function __construct(
        PartialFactory $partialFactory,
        RenderableDataFactoryInterface $dataFactory,
        NavigationLinkFactory $linkFactory
    ) {
        parent::__construct($partialFactory, $dataFactory);
        $this->linkFactory = $linkFactory;
        $this->initializeTemplateFile(self::DEFAULT_TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    public function addNavigationLink(
        string $label,
        string $url,
        bool $visible = true,
        bool $active = false,
        string $iconClass = ''
    ): self {
        $this->links[] = $this->linkFactory->createNavigationLink($label, $url, $visible, $active, $iconClass);
        $this->isConfigured = true;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function setNavigationLinks(array $links): self
    {
        $this->links = [];

        if (!empty($links) && array_keys($links) !== range(0, count($links) - 1)) {
            $this->links = [$links];
        } else {
            $this->links = $links;
        }

        $this->isConfigured = true;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isReady(): bool
    {
        return $this->isConfigured && !empty($this->links) && !empty($this->templateFile);
    }

    /**
     * {@inheritdoc}
     */
    public function build(): NavigationInterface
    {
        $this->checkReadyState();
        return new Navigation(
            $this->templateFile,
            $this->buildLinksCollection(),
            $this->buildDataFromArray($this->data),
            $this->buildPartialsCollection($this->partials),
        );
    }

    /**
     * Builds a NavigationLinkCollection from the links array.
     */
    private function buildLinksCollection(): NavigationLinkCollectionInterface
    {
        return $this->linkFactory->createNavigationLinkCollection($this->links);
    }
}
