<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

/**
 * Patches the project's `compose.yaml` so docker compose emits the Hydra
 * `apiDocumentation` + Mercure Link headers on every response.
 *
 * Mirrors Symfony Flex's recipe-block strategy: the patch is wrapped in
 * `###> api-platform/api-platform ###` / `###< api-platform/api-platform ###`
 * markers and applied as raw text. No yaml round-trip — upstream comments,
 * blank lines and key ordering are preserved verbatim.
 */
final class ComposeOverrideWriter
{
    public const CADDY_DIRECTIVES = 'header ?Link `</docs.jsonld>; rel="http://www.w3.org/ns/hydra/core#apiDocumentation", </.well-known/mercure>; rel="mercure"`';
    public const RECIPE_NAME = 'api-platform/api-platform';
    public const START_MARKER = '###> '.self::RECIPE_NAME.' ###';
    public const END_MARKER = '###< '.self::RECIPE_NAME.' ###';

    // Upstream symfony-docker has historically named the Caddy/PHP service
    // `php`; recent images may rename it to `frankenphp` (matching the image
    // name). Walk through every known candidate when locating environment.
    private const PHP_SERVICE_CANDIDATES = ['php', 'frankenphp'];

    public function write(string $apiDir): void
    {
        $file = $apiDir.'/compose.yaml';
        if (!is_file($file)) {
            throw new \RuntimeException(sprintf('Could not find %s.', $file));
        }

        $contents = (string) file_get_contents($file);
        $patched = str_contains($contents, self::START_MARKER)
            ? $this->rewriteBlock($contents)
            : $this->insertBlock($contents, $file);

        if ($patched !== $contents) {
            file_put_contents($file, $patched);
        }
    }

    private function rewriteBlock(string $contents): string
    {
        $pattern = '/(\h*)'.preg_quote(self::START_MARKER, '/').'.*?'.preg_quote(self::END_MARKER, '/').'/s';

        return (string) preg_replace_callback(
            $pattern,
            static fn (array $m): string => self::renderBlock($m[1]),
            $contents,
            1,
        );
    }

    private function insertBlock(string $contents, string $file): string
    {
        $lines = preg_split('/\R/', $contents) ?: [];
        $sectionEnd = $this->findEnvironmentSectionEnd($lines);
        if (null === $sectionEnd) {
            throw new \RuntimeException(sprintf(
                'Could not find services.{%s}.environment in %s; cannot inject the Hydra+Mercure Link header.',
                implode('|', self::PHP_SERVICE_CANDIDATES),
                $file,
            ));
        }

        array_splice($lines, $sectionEnd, 0, explode("\n", self::renderBlock('      ')));

        return implode("\n", $lines);
    }

    /**
     * Returns the 0-based index of the first line AFTER `services.php.environment`
     * (i.e. the row where a sibling key like `volumes:` or end-of-file begins).
     *
     * @param list<string> $lines
     */
    private function findEnvironmentSectionEnd(array $lines): ?int
    {
        $servicesAt = $this->findKey($lines, 0, count($lines), '', 'services');
        if (null === $servicesAt) {
            return null;
        }
        $servicesEnd = $this->findSectionEnd($lines, $servicesAt + 1, 0);

        foreach (self::PHP_SERVICE_CANDIDATES as $service) {
            $phpAt = $this->findKey($lines, $servicesAt + 1, $servicesEnd, '  ', $service);
            if (null === $phpAt) {
                continue;
            }
            $phpEnd = $this->findSectionEnd($lines, $phpAt + 1, 2);

            $envAt = $this->findKey($lines, $phpAt + 1, $phpEnd, '    ', 'environment');
            if (null === $envAt) {
                continue;
            }

            return $this->findSectionEnd($lines, $envAt + 1, 4);
        }

        return null;
    }

    /**
     * Locate a key declared at exactly `$indent` between `$from` and `$to`.
     *
     * @param list<string> $lines
     */
    private function findKey(array $lines, int $from, int $to, string $indent, string $key): ?int
    {
        $pattern = '/^'.preg_quote($indent, '/').preg_quote($key, '/').':\s*$/';
        for ($i = $from; $i < $to; ++$i) {
            if (preg_match($pattern, $lines[$i])) {
                return $i;
            }
        }

        return null;
    }

    /**
     * Walk forward from `$from` and stop at the first line whose indent is
     * shallower than `$parentIndent + 1`. Blank lines stay inside the section.
     *
     * @param list<string> $lines
     */
    private function findSectionEnd(array $lines, int $from, int $parentIndent): int
    {
        $total = count($lines);
        for ($i = $from; $i < $total; ++$i) {
            $line = $lines[$i];
            if ('' === trim($line)) {
                continue;
            }
            $leading = strspn($line, ' ');
            if ($leading <= $parentIndent) {
                return $i;
            }
        }

        return $total;
    }

    private static function renderBlock(string $indent): string
    {
        return $indent.self::START_MARKER."\n"
            .$indent."CADDY_SERVER_EXTRA_DIRECTIVES: '".self::CADDY_DIRECTIVES."'\n"
            .$indent.self::END_MARKER;
    }
}
