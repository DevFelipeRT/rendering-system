<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Context\Dto;

use Rendering\Infrastructure\Contract\RenderingEngine\Context\RenderContextInterface;

/**
 * A Data Transfer Object that encapsulates the prepared data for a template's scope.
 *
 * It extends the AbstractContext to inherit common, collection-like helper methods.
 */
final class RenderContext extends AbstractContext implements RenderContextInterface
{
    /**
     * @param array<string, mixed> $data The data to be passed to the template.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        return $this->all();
    }
}