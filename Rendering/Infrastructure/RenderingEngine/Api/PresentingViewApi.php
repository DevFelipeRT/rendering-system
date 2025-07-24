<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Api;

/**
 * The ViewApi implementation for the final presentation stage.
 */
final class PresentingViewApi extends AbstractViewApi
{
    /**
     * A cache to store the fully resolved content of sections to avoid re-processing.
     * @var array<string, string>
     */
    private array $resolvedSectionsCache = [];

    /**
     * {@inheritdoc}
     */
    public function extend(string $layoutName): void
    {
        // No-op during presentation
    }

    /**
     * {@inheritdoc}
     */
    public function startSection(string $sectionName): void
    {
        // No-op during presentation
    }

    /**
     * {@inheritdoc}
     */
    public function stopSection(): void
    {
        // No-op during presentation
    }

    /**
     * {@inheritdoc}
     *
     * Renders the final, fully-composed content of a named section.
     * This optimized method relies on a cache to prevent infinite recursion and
     * uses a single, high-performance string replacement operation.
     */
    public function yieldSection(string $sectionName): string
    {
        // 1. (GUARD & CACHE HIT) If this section has already been fully resolved,
        //    return it immediately. This is the base case for the recursion.
        if (isset($this->resolvedSectionsCache[$sectionName])) {
            return $this->resolvedSectionsCache[$sectionName];
        }

        // 2. Get the raw content of the section, which may contain placeholders.
        $content = $this->renderState->getSection($sectionName);

        // 3. Find which placeholders are actually inside this specific content string.
        //    This avoids trying to replace placeholders that don't exist here.
        $placeholdersToReplace = [];
        foreach ($this->renderState->getYieldPlaceholders() as $id => $nestedSection) {
            if (str_contains($content, $id)) {
                $placeholdersToReplace[$id] = $nestedSection;
            }
        }

        // 4. If there's nothing to replace in this specific content, cache and return it.
        if (empty($placeholdersToReplace)) {
            return $this->resolvedSectionsCache[$sectionName] = $content;
        }
        
        // 5. Prepare for a single, high-performance replacement.
        $search = [];
        $replace = [];

        foreach ($placeholdersToReplace as $placeholderId => $nestedSectionName) {
            $search[] = $placeholderId;
            // Recursively call to get the fully resolved content of the nested section.
            // The cache check at the beginning (step 1) is the crucial guard
            // that protects this from the original infinite loop error.
            $replace[] = $this->yieldSection($nestedSectionName);
        }
        
        // 6. Perform a single, fast replacement for all found placeholders.
        $resolvedContent = str_replace($search, $replace, $content);

        // 7. Cache the final, fully resolved result and return it.
        return $this->resolvedSectionsCache[$sectionName] = $resolvedContent;
    }

    /**
     * {@inheritdoc}
     */
    public function startPush(string $stackName): void
    {
        // No-op during presentation
    }

    /**
     * {@inheritdoc}
     */
    public function stopPush(): void
    {
        // No-op during presentation
    }

    /**
     * {@inheritdoc}
     */
    public function renderStack(string $stackName): string
    {
        return $this->renderState->renderStack($stackName);
    }

    /**
     * {@inheritdoc}
     */
    public function shouldRenderOnce(string $id): bool
    {
        // No-op during presentation
        return false;
    }
}
