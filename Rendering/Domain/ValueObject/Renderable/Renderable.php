<?php

declare(strict_types=1);

namespace Rendering\Domain\ValueObject\Renderable;

use Rendering\Domain\Contract\ValueObject\Renderable\RenderableDataInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;
use Rendering\Domain\Trait\Validation\TemplateFileValidationTrait;

/**
 * A base class for renderable components.
 *
 * This class provides a foundation for any component that needs to be rendered.
 * It encapsulates a template file path and its corresponding data object, serving as
 * a base implementation that can be extended by concrete renderable classes.
 */
class Renderable implements RenderableInterface
{
    use TemplateFileValidationTrait;

    /**
     * @var string The template file name.
     * Must be a non-empty string.
     */
    protected readonly string $templateFile;

    /**
     * @var RenderableDataInterface|null The data object for the template.
     * Can be null if no data is provided.
     */
    protected readonly ?RenderableDataInterface $dataProvider;

    /**
     * Constructs a new Renderable instance.
     *
     * @param string $templateFile The path to the template file. Must be a non-empty string.
     * @param RenderableDataInterface $dataProvider The optional data object for the template.
     */
    public function __construct(
        string $templateFile, 
        ?RenderableDataInterface $dataProvider = null,
    ){
        $this->validateTemplateFile($templateFile);
        $this->templateFile = $templateFile;
        $this->dataProvider = $dataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function fileName(): string
    {
        return $this->templateFile;
    }

    /**
     * {@inheritdoc}
     */
    public function data(): ?RenderableDataInterface
    {
        return $this->dataProvider;
    }
}
