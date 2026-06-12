<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

use RuntimeException;

/**
 * Throwing wrapper around file_put_contents so a failed write (permissions,
 * disk full) aborts the scaffold instead of being reported as success.
 */
final class FileWriter
{
    public static function write(string $path, string $contents): void
    {
        if (false === @file_put_contents($path, $contents)) {
            throw new RuntimeException(sprintf('Could not write %s.', $path));
        }
    }
}
