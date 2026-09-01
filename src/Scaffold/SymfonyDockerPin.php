<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

use InvalidArgumentException;
use RuntimeException;

/**
 * Reads and rewrites the symfony-docker commit pinned by {@see SymfonyScaffold},
 * and tells which upstream paths the scaffold actually copies.
 *
 * Lives here so the bump workflow never has to restate the pin format or the
 * copy list in YAML, where it would silently drift from the scaffold.
 */
final class SymfonyDockerPin
{
    private const PATTERN = "/(SYMFONY_DOCKER_REF = ')([0-9a-f]{40})(')/";

    public static function read(string $source): string
    {
        if (!preg_match(self::PATTERN, $source, $m)) {
            throw new RuntimeException('Could not find a pinned SYMFONY_DOCKER_REF; the constant was renamed or reformatted.');
        }

        return $m[2];
    }

    public static function replace(string $source, string $sha): string
    {
        if (!preg_match('/^[0-9a-f]{40}$/', $sha)) {
            throw new InvalidArgumentException(sprintf('Expected a full 40-character commit SHA, got "%s".', $sha));
        }

        $patched = (string) preg_replace(self::PATTERN, '${1}'.$sha.'${3}', $source, 1);
        if ($patched === $source) {
            throw new RuntimeException(sprintf('SYMFONY_DOCKER_REF is already pinned to %s.', $sha));
        }

        return $patched;
    }

    /**
     * @param list<string> $changedPaths upstream paths, relative to the symfony-docker root
     *
     * @return list<string>
     */
    public static function copiedPaths(array $changedPaths): array
    {
        return array_values(array_filter($changedPaths, static function (string $path): bool {
            foreach (SymfonyScaffold::DOCKER_FILES as $copied) {
                if ($path === $copied || str_starts_with($path, $copied.'/')) {
                    return true;
                }
            }

            return false;
        }));
    }
}
