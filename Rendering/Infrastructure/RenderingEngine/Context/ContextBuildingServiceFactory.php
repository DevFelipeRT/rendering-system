<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Context;

use Rendering\Domain\Contract\ValueObject\Renderable\Page\PageInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\FooterInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\HeaderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialViewInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\View\ViewInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\Context\ContextBuildingServiceInterface;
use Rendering\Infrastructure\RenderingEngine\Context\Builder\PageContextBuilder;
use Rendering\Infrastructure\RenderingEngine\Context\Builder\Partial\FooterContextBuilder;
use Rendering\Infrastructure\RenderingEngine\Context\Builder\Partial\HeaderContextBuilder;
use Rendering\Infrastructure\RenderingEngine\Context\Builder\Partial\NavigationContextBuilder;
use Rendering\Infrastructure\RenderingEngine\Context\Builder\Partial\PartialContextBuilder;
use Rendering\Infrastructure\RenderingEngine\Context\Builder\ViewContextBuilder;

/**
 * Factory responsible for creating a fully configured ContextBuilder orchestrator.
 *
 * This class encapsulates the logic of assembling the dispatch map of specialized
 * context builders, keeping the service container or bootstrap file clean.
 */
final class ContextBuildingServiceFactory
{
    /**
     * Creates and configures the ContextBuildingService orchestrator with all its specialist dependencies.
     */
    public function create(): ContextBuildingServiceInterface
    {
        // The order of this map is crucial for the lookup logic.
        // More specific interfaces must come before their generic parents.
        $specialistBuilders = [
            HeaderInterface::class => new HeaderContextBuilder(),
            FooterInterface::class => new FooterContextBuilder(),
            NavigationInterface::class => new NavigationContextBuilder(),
            PartialViewInterface::class => new PartialContextBuilder(),
            ViewInterface::class => new ViewContextBuilder(),
            PageInterface::class => new PageContextBuilder(),
        ];

        return new ContextBuildingService($specialistBuilders);
    }
}
