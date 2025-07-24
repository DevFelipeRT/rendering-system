<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Building\Renderable\Partial;

use Rendering\Domain\Contract\Service\Building\Partial\FooterBuilderInterface;
use Rendering\Domain\Contract\Service\Building\Partial\HeaderBuilderInterface;
use Rendering\Domain\Contract\Service\Building\Partial\NavigationBuilderInterface;
use Rendering\Domain\Contract\Service\Building\Partial\PartialBuilderInterface;
use Rendering\Domain\Contract\Service\Building\Partial\PartialBuildingServiceInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\FooterInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\HeaderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialViewInterface;

/**
 * A specialized service that handles all partial-related operations.
 *
 * This service encapsulates the logic for creating and managing partial views
 * across different page components (page, view, header, footer). It provides
 * a clean separation of concerns by extracting partial management from the
 * main BuildingService, making the codebase more modular and maintainable.
 */
final class PartialBuildingService implements PartialBuildingServiceInterface
{
    /**
     * @param HeaderBuilderInterface $headerBuilder The builder for the Header component.
     * @param FooterBuilderInterface $footerBuilder The builder for the Footer component.
     * @param NavigationBuilderInterface $navigationBuilder The builder for the Navigation component.
     * @param PartialBuilderInterface $partialBuilder A generic builder for nested partials.
     */
    public function __construct(
        private readonly HeaderBuilderInterface $headerBuilder,
        private readonly FooterBuilderInterface $footerBuilder,
        private readonly NavigationBuilderInterface $navigationBuilder,
        private readonly PartialBuilderInterface $partialBuilder
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function createHeader(
        string $templateFile,
        array $data = [],
        array $partials = [],
        ?NavigationInterface $navigation = null
    ): HeaderInterface {
        $this->headerBuilder
            ->setTemplateFile($templateFile)
            ->setData($data)
            ->setPartials($partials);

        if ($navigation !== null) {
            $this->headerBuilder->setNavigation($navigation);
        }

        return $this->headerBuilder->build();
    }

    /**
     * {@inheritdoc}
     */
    public function createFooter(
        string $templateFile, 
        array $data = [], 
        array $partials = [],
        ?string $copyrightOwner = null,
        ?string $copyrightMessage = 'All rights reserved.'
    ): FooterInterface {
        $this->footerBuilder
            ->setTemplateFile($templateFile)
            ->setData($data)
            ->setPartials($partials);

        if ($copyrightOwner !== null) {
            $this->footerBuilder->setCopyright($copyrightOwner, $copyrightMessage);
        }

        return $this->footerBuilder->build();
    }

    /**
     * {@inheritdoc}
     */
    public function createNavigation(
        string $templateFile, 
        array $data = [], 
        array $partials = [],
        array $links = []
    ): NavigationInterface {
        return $this->navigationBuilder
            ->setNavigationLinks($links)
            ->setTemplateFile($templateFile)
            ->setData($data)
            ->setPartials($partials)
            ->build();
    }

    /**
     * {@inheritdoc}
     */
    public function createPartialView(
        string $templateFile, 
        array $data = [], 
        array $partials = []
    ): PartialViewInterface {
        return $this->partialBuilder
            ->setTemplateFile($templateFile)
            ->setData($data)
            ->setPartials($partials)
            ->build();
    }

    /**
     * Gets the partial builder for advanced construction scenarios.
     *
     * @return PartialBuilderInterface The partial builder.
     */
    public function partialBuilder(): PartialBuilderInterface
    {
        return $this->partialBuilder;
    }

    /**
     * Gets the header builder for internal component building.
     *
     * @return HeaderBuilderInterface
     */
    public function headerBuilder(): HeaderBuilderInterface
    {
        return $this->headerBuilder;
    }

    /**
     * Gets the footer builder for internal component building.
     *
     * @return FooterBuilderInterface
     */
    public function footerBuilder(): FooterBuilderInterface
    {
        return $this->footerBuilder;
    }

    /**
     * Gets the navigation builder for internal component building.
     *
     * @return NavigationBuilderInterface
     */
    public function navigationBuilder(): NavigationBuilderInterface
    {
        return $this->navigationBuilder;
    }
}
