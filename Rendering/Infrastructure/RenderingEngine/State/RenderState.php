<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\State;

use LogicException;
use Rendering\Infrastructure\Contract\RenderingEngine\State\RenderStateInterface;

/**
 * Manages the state for a single, ephemeral rendering operation.
 *
 * This class is the central repository for all data collected during the
 * POPULATE rendering stage, including the layout inheritance chain, the
 * content of all defined sections, and the content pushed to stacks.
 */
final class RenderState implements RenderStateInterface
{
    /**
     * Stores the name of the parent layout declared by an @extends directive.
     */
    private ?string $parentLayout = null;

    /**
     * A stack to keep track of which sections are currently being captured.
     * @var string[]
     */
    private array $sectionStack = [];

    /**
     * Stores the captured content for each named section.
     * @var array<string, string>
     */
    private array $sections = [];

    /**
     * Stores content for named stacks.
     * @var array<string, string[]>
     */
    private array $stacks = [];

    /**
     * Tracks the hierarchy of active push operations.
     * @var string[]
     */
    private array $pushStack = [];

    /**
     * Stores the hashes of @once blocks that have already been rendered.
     * @var array<string, true>
     */
    private array $onceHashes = [];

    /**
     * {@inheritdoc}
     */
    public function setParent(string|null $layoutName): void
    {
        $this->parentLayout = $layoutName;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return $this->parentLayout;
    }

    /**
     * {@inheritdoc}
     */
    public function startSection(string $sectionName): void
    {
        $this->sectionStack[] = $sectionName;
        ob_start();
    }

    /**
     * {@inheritdoc}
     */
    public function stopSection(): void
    {
        if (empty($this->sectionStack)) {
            throw new LogicException('Cannot call @endsection without a matching @section.');
        }

        $lastSection = array_pop($this->sectionStack);
        $this->sections[$lastSection] = ob_get_clean();
    }

/**
     * {@inheritdoc}
     */
    public function getSection(string $sectionName): string
    {
        return $this->sections[$sectionName] ?? '';
    }

    /**
     * {@inheritdoc}
     */
    public function startPush(string $stackName): void
    {
        ob_start();
        $this->pushStack[] = $stackName;
    }

    /**
     * {@inheritdoc}
     */
    public function stopPush(): void
    {
        if (empty($this->pushStack)) {
            throw new LogicException('Cannot call @endpush without a matching @push.');
        }

        $stackName = array_pop($this->pushStack);
        $content = ob_get_clean();
        $this->stacks[$stackName][] = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function renderStack(string $stackName): string
    {
        return implode('', $this->stacks[$stackName] ?? []);
    }

    /**
     * {@inheritdoc}
     */
    public function shouldRenderOnce(string $id): bool
    {
        if (isset($this->onceHashes[$id])) {
            return false;
        }

        $this->onceHashes[$id] = true;
        return true;
    }
}