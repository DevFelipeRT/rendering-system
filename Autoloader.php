<?php

declare(strict_types=1);

/**
 * A PSR-4 compliant class autoloader.
 *
 * This autoloader encapsulates the logic for mapping namespace prefixes to
 * base directories, providing a robust, dependency-free mechanism for loading
 * classes and interfaces on demand.
 */
final class Autoloader
{
    /**
     * An associative array where the key is a namespace prefix and the value
     * is the base directory for classes in that namespace.
     *
     * @var array<string, string>
     */
    private array $prefixes = [];

    /**
     * Registers the autoloader instance with the SPL autoloader stack.
     *
     * @return void
     */
    public function register(): void
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    /**
     * Adds a namespace prefix to base directory mapping.
     *
     * @param string $prefix The namespace prefix (e.g., "Logging\\").
     * @param string $baseDir The base directory for class files in the namespace.
     * @return void
     */
    public function addNamespace(string $prefix, string $baseDir): void
    {
        // Normalize namespace prefix
        $prefix = trim($prefix, '\\') . '\\';

        // Normalize the base directory with a trailing separator
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        // Store the mapping
        $this->prefixes[$prefix] = $baseDir;
    }

    /**
     * Loads the class file for a given class name.
     *
     * This method is the callback registered with spl_autoload_register.
     *
     * @param string $className The fully qualified class name.
     * @return bool True on success, false on failure.
     */
    public function loadClass(string $className): bool
    {
        foreach ($this->prefixes as $prefix => $baseDir) {
            // Check if the class uses this namespace prefix
            if (str_starts_with($className, $prefix)) {
                // Get the relative class name
                $relativeClass = substr($className, strlen($prefix));

                // Replace the namespace prefix with the base directory,
                // replace namespace separators with directory separators
                // in the relative class name, and append with .php
                $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

                // If the file exists and is readable, require it
                return $this->requireFile($file);
            }
        }

        return false;
    }

    /**
     * Requires a file if it is readable.
     *
     * @param string $file The file to require.
     * @return bool True if the file was required, false otherwise.
     */
    private function requireFile(string $file): bool
    {
        if (is_readable($file)) {
            require $file;
            return true;
        }

        return false;
    }
}