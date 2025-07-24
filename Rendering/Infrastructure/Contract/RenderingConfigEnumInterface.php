<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Contract;

/**
 * Interface for rendering configuration enums.
 *
 * This interface defines the contract that any enum providing rendering
 * configuration values must implement, allowing developers to create
 * their own configuration enums and inject them into RenderingConfig.
 */
interface RenderingConfigEnumInterface
{
    /**
     * Returns the configuration value for this enum case.
     *
     * @return mixed The configuration value (string, int, array, etc.)
     */
    public function getValue(): mixed;

    /**
     * Returns the configuration key/identifier for this enum case.
     *
     * This should return a consistent identifier that can be used
     * to determine what type of configuration this enum case represents.
     *
     * @return string
     */
    public function getConfigKey(): string;
}
