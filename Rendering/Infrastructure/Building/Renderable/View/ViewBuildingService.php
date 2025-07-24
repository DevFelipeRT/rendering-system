<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Building\Renderable\View;

use Rendering\Domain\Contract\Service\Building\View\ViewBuildingServiceInterface;
use Rendering\Domain\Contract\Service\Building\View\ViewBuilderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\View\ViewInterface;

/**
 * ViewBuildingService provides factory methods for creating view components.
 * 
 * This service encapsulates the complexity of view construction and provides
 * a simplified API for common view creation scenarios while maintaining
 * access to the underlying builder for advanced use cases.
 */
class ViewBuildingService implements ViewBuildingServiceInterface
{
    /**
     * Constructs a new ViewBuildingService instance.
     *
     * @param ViewBuilderInterface $viewBuilder The view builder instance.
     */
    public function __construct(
        private readonly ViewBuilderInterface $viewBuilder
    ) {}

    /**
     * {@inheritdoc}
     */
    public function createView(
        string $templateFile, 
        array $data = [], 
        array $partials = [],
        ?string $title = null,
        ?string $description = null
    ): ViewInterface {
        return $this->viewBuilder
            ->setTitle($title)
            ->setDescription($description)
            ->setTemplateFile($templateFile)
            ->setData($data)
            ->setPartials($partials)
            ->build();
    }

    /**
     * {@inheritdoc}
     */
    public function viewBuilder(): ViewBuilderInterface
    {
        return $this->viewBuilder;
    }
}
