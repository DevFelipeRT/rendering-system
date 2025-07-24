<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Renderer;

use Rendering\Domain\Contract\Service\RenderingEngine\RendererInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\TemplateEngineInterface;
use Rendering\Infrastructure\Contract\TemplateProcessing\TemplateProcessingServiceInterface;
use Rendering\Infrastructure\RenderingEngine\Exception\RenderingException;
use Throwable;

/**
 * Provides foundational infrastructure for rendering templates.
 *
 * This class is a pure "worker", responsible only for resolving a template
 * name to a compiled path and executing it with the provided data. It has
 * no knowledge of the rendering service or the ViewApi.
 */
abstract class AbstractRenderer implements RendererInterface
{
    /**
     * @param TemplateEngineInterface $templateEngine
     * @param TemplateProcessingServiceInterface $templateProcessor
     * @param bool $debug
     */
    public function __construct(
        protected readonly TemplateEngineInterface $templateEngine,
        protected readonly TemplateProcessingServiceInterface $templateProcessor,
        protected readonly bool $debug = false,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function render(RenderableInterface $renderable, array $data): string
    {
        $templateName = $this->getTemplateTarget($renderable);
        return $this->executeRendering($templateName, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function renderTemplate(string $templateFile, array $data = []): string
    {
        return $this->executeRendering($templateFile, $data);
    }
    
    /**
     * Retrieves the template target from the renderable object.
     *
     * @param RenderableInterface $renderable
     * @return string
     */
    protected function getTemplateTarget(RenderableInterface $renderable): string
    {
        return $renderable->fileName();
    }

    /**
     * Orchestrates the final rendering pipeline: resolve path and execute engine.
     *
     * @param string $templateName
     * @param array $data
     * @return string
     */
    private function executeRendering(string $templateName, array $data): string
    {
        try {
            $compiledPath = $this->resolveTemplatePath($templateName);
            return $this->executeEngine($compiledPath, $data);
        } catch (Throwable $e) {
            if ($this->debug) {
                if ($e instanceof RenderingException) {
                    throw $e;
                }
                throw RenderingException::forGenericFailure($templateName, $e);
            }
            return '';
        }
    }
    
    /**
     * Resolves a template name to its compiled path.
     *
     * @param string $templateName
     * @return string
     * @throws RenderingException
     */
    private function resolveTemplatePath(string $templateName): string
    {
        try {
            return $this->templateProcessor->resolve($templateName);
        } catch (Throwable $e) {
            throw RenderingException::forTemplateResolution($templateName, $e);
        }
    }

    /**
     * Executes the template engine with the given data.
     *
     * @param string $compiledPath
     * @param array $data
     * @return string
     * @throws RenderingException
     */
    private function executeEngine(string $compiledPath, array $data): string
    {
        try {
            return $this->templateEngine->execute($compiledPath, $data);
        } catch (Throwable $e) {
            throw RenderingException::forEngineExecution($compiledPath, $e);
        }
    }
}