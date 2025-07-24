<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Building\Renderable\Partial;

use Rendering\Domain\Contract\Service\Building\Partial\FooterBuilderInterface;
use Rendering\Domain\Contract\Service\Building\Partial\HeaderBuilderInterface;
use Rendering\Domain\Contract\Service\Building\Partial\NavigationBuilderInterface;
use Rendering\Domain\Contract\Service\Building\Partial\PartialBuilderInterface;
use Rendering\Domain\Contract\Service\Building\Partial\PartialBuildingServiceInterface;

/**
 * Factory responsible for creating a configured PartialBuildingService.
 *
 * This factory encapsulates the instantiation logic for the partial building
 * service, providing all necessary builder dependencies.
 */
final class PartialBuildingServiceFactory
{
    /**
     * Creates a fully configured PartialBuildingService with all its dependencies.
     *
     * @param HeaderBuilderInterface $headerBuilder The builder for the Header component.
     * @param FooterBuilderInterface $footerBuilder The builder for the Footer component.
     * @param NavigationBuilderInterface $navigationBuilder The builder for the Navigation component.
     * @param PartialBuilderInterface $partialBuilder A generic builder for nested partials.
     * @return PartialBuildingServiceInterface The configured partial building service.
     */
    public static function create(
        HeaderBuilderInterface $headerBuilder,
        FooterBuilderInterface $footerBuilder,
        NavigationBuilderInterface $navigationBuilder,
        PartialBuilderInterface $partialBuilder
    ): PartialBuildingServiceInterface {
        return new PartialBuildingService(
            $headerBuilder,
            $footerBuilder,
            $navigationBuilder,
            $partialBuilder
        );
    }
}
