<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Building\Renderable\Trait;

use Rendering\Domain\ValueObject\Renderable\Partial\PartialsCollection;
use Rendering\Infrastructure\Building\Renderable\Partial\Factory\PartialFactory;

trait BuilderPartialHandlingTrait
{
    protected PartialFactory $partialFactory;
    protected array $partials = [];

    /**
     * Adds a partial to the builder.
     *
     * @param string $key The key for the partial.
     * @param mixed $partial The partial data.
     * @return self
     */
    public function addPartial(string $key, mixed $partial): self
    {
        $this->partials[$key] = $partial;
        $this->markAsConfigured();
        return $this;
    }

    /**
     * Sets all partials for the builder.
     *
     * @param array $partials The array of partials.
     * @return self
     */
    public function setPartials(array $partials): self
    {
        $this->partials = $partials;
        $this->markAsConfigured();
        return $this;
    }

    /**
     * Hydrates an array of partials into a PartialsCollection.
     *
     * @param array $partials The array of partial definitions.
     * @return PartialsCollection|null A collection of hydrated PartialView objects or null if the input is empty.
     */
    protected function buildPartialsCollection(array $partials): ?PartialsCollection
    {
        if (empty($partials)) {
            return null;
        }
        
        return $this->partialFactory->createPartialsCollection($partials);
    }

    /**
     * Abstract method to mark the builder as configured.
     * Must be implemented by the class using this trait.
     */
    abstract protected function markAsConfigured(): void;
}