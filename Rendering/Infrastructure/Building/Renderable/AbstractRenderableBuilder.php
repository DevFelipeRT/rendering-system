<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Building\Renderable;

use Rendering\Domain\Contract\Service\Building\RenderableBuilderInterface;
use InvalidArgumentException;
use Rendering\Infrastructure\Contract\Factory\ValueObject\RenderableDataFactoryInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableDataInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;
use Rendering\Domain\ValueObject\Renderable\Renderable;
use Rendering\Infrastructure\Building\Partial\Factory\PartialFactory;

abstract class AbstractRenderableBuilder implements RenderableBuilderInterface
{
    protected RenderableDataFactoryInterface $dataFactory;
    protected string $templateFile = '';
    protected array $data = [];

    /**
     * A flag to track if the builder has been configured by the client.
     * @var bool
     */
    protected bool $isConfigured = false;

    /**
     * Constructor to initialize the builder with necessary factories.
     *
     * @param PartialFactory $partialFactory Factory for creating partials.
     * @param RenderableDataFactoryInterface $dataFactory Factory for creating renderable data.
     */
    public function __construct(
        RenderableDataFactoryInterface $dataFactory
    ) {
        $this->dataFactory = $dataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateFile(string $templateFile): self
    {
        if (trim($templateFile) === '') {
            throw new InvalidArgumentException('Template file name cannot be empty.');
        }
        $this->templateFile = $templateFile;
        $this->markAsConfigured();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        $this->markAsConfigured();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isReady(): bool
    {
        // A builder is considered "ready" if it has been explicitly configured
        // in any way and has a valid template file associated with it.
        return $this->isConfigured && !empty($this->templateFile);
    }

    /**
     * {@inheritdoc}
     */
    public function build(): RenderableInterface
    {
        $this->checkReadyState();

        return new Renderable(
            $this->templateFile,
            $this->buildDataFromArray($this->data)
        );
    }

    /**
     * Checks if the builder is ready to build a Renderable object.
     *
     * This method should be called before building to ensure that all necessary
     * configurations have been made, such as setting a template file and data.
     *
     * @throws \LogicException If the builder is not ready.
     */
    protected function checkReadyState(): void
    {
        if (!$this->isReady()) {
            throw new \LogicException('Builder is not ready. Ensure it has been configured properly.');
        }
    }

    /**
     * Initializes the default template file without marking the builder as configured.
     * This method is intended for use within the constructors of child builders.
     *
     * @param string $templateFile The default template file path.
     */
    protected function initializeTemplateFile(string $templateFile): void
    {
        if (trim($templateFile) === '') {
            throw new InvalidArgumentException('Default template file name cannot be empty.');
        }
        $this->templateFile = $templateFile;
    }

    /**
     * Converts an associative array into a RenderableData object.
     *
     * @param array $data The associative array to convert.
     * @return RenderableDataInterface|null The RenderableData object or null if the array is empty.
     */
    protected function buildDataFromArray(array $data): ?RenderableDataInterface
    {
        if (empty($data)) {
            return null;
        }
        return $this->dataFactory->createFromArray($data);
    }

    /**
     * Marks the builder as configured.
     */
    protected function markAsConfigured(): void
    {
        $this->isConfigured = true;
    }
}