<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

use Symfony\Component\Yaml\Yaml;

/**
 * Writes a self-contained `compose.api-platform.yaml` and configures
 * `docker compose` to merge it via COMPOSE_FILE in `.env`. Upstream
 * symfony-docker files stay verbatim, comments and all.
 *
 * For prod (`docker compose -f compose.yaml -f compose.prod.yaml`),
 * users must pass `-f compose.api-platform.yaml` manually — `-f` flags
 * supersede COMPOSE_FILE.
 */
final class ComposeOverrideWriter
{
    public const CADDY_DIRECTIVES = 'header ?Link `</docs.jsonld>; rel="http://www.w3.org/ns/hydra/core#apiDocumentation", </.well-known/mercure>; rel="mercure"`';
    public const COMPOSE_FILE_LINE = "\nCOMPOSE_FILE=compose.yaml:compose.override.yaml:compose.api-platform.yaml\n";

    public function buildYaml(): string
    {
        return Yaml::dump([
            'services' => [
                'php' => [
                    'environment' => [
                        'CADDY_SERVER_EXTRA_DIRECTIVES' => self::CADDY_DIRECTIVES,
                    ],
                ],
            ],
        ], 4, 2);
    }

    public function write(string $apiDir): void
    {
        $envFile = $apiDir.'/.env';
        if (!is_file($envFile)) {
            throw new \RuntimeException(sprintf('Could not find %s.', $envFile));
        }

        file_put_contents($apiDir.'/compose.api-platform.yaml', $this->buildYaml());

        $env = (string) file_get_contents($envFile);
        if (!preg_match('/^COMPOSE_FILE=/m', $env)) {
            file_put_contents($envFile, self::COMPOSE_FILE_LINE, \FILE_APPEND);
        }
    }
}
