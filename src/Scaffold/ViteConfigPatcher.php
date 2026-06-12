<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

/**
 * Adds a new entry to the `input` array of a Laravel `vite.config.js`,
 * wrapped in Flex-style markers so re-runs are idempotent and upgrades
 * can rewrite the block in place.
 */
final class ViteConfigPatcher
{
    public const RECIPE_NAME = AdminRecipe::NAME;
    public const START_MARKER = '/* ###> '.self::RECIPE_NAME.' ### */';
    public const END_MARKER = '/* ###< '.self::RECIPE_NAME.' ### */';

    public function patch(string $config, string $entry): string
    {
        if (str_contains($config, self::START_MARKER)) {
            return $config;
        }

        $injection = sprintf("%s '%s' %s", self::START_MARKER, addcslashes($entry, "\\'"), self::END_MARKER);

        $count = 0;
        $patched = (string) preg_replace_callback(
            '/(input:\s*\[)([^\]]*)(\])/',
            static function (array $m) use ($injection): string {
                // Strip trailing whitespace then a trailing comma (and the
                // whitespace it may have hidden) so injecting `, $injection`
                // never produces `,,` — an empty array element in JS.
                $existing = rtrim(rtrim(rtrim($m[2]), ','));
                $sep = '' === $existing ? '' : ', ';

                return $m[1].$existing.$sep.$injection.$m[3];
            },
            $config,
            1,
            $count,
        );

        if (0 === $count) {
            throw new \RuntimeException('Could not find `input: [...]` in vite.config.js; cannot inject the admin entry.');
        }

        return $patched;
    }
}
