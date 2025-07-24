<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Context\Dto;

use Rendering\Infrastructure\Contract\RenderingEngine\Context\ApiContextInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;

/**
 * A Data Transfer Object that encapsulates the contextual objects for the ViewApi.
 *
 * It extends the AbstractContext to inherit common, collection-like helper methods.
 */
final class ApiContext extends AbstractContext implements ApiContextInterface
{
    /**
     * @param array<RenderableInterface>|null $contextObjects The array of context objects.
     */
    public function __construct(?array $contextObjects)
    {
        $this->data = $contextObjects ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function getContextObjects(): array
    {
        return $this->all();
    }
}