<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Tests\Scaffold;

use ApiPlatform\Installer\Scaffold\ComposeOverrideWriter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class ComposeOverrideWriterTest extends TestCase
{
    private const UPSTREAM_COMPOSE = <<<'YAML'
        ---
        services:
          php:
            restart: unless-stopped
            environment:
              SERVER_NAME: ${SERVER_NAME:-localhost}, php:80
              # Run "composer require symfony/orm-pack" to install and configure Doctrine ORM
              DATABASE_URL: postgresql://app:pass@database:5432/app
              # Run "composer require symfony/mercure-bundle" to install and configure the Mercure integration
              MERCURE_URL: ${CADDY_MERCURE_URL:-http://php/.well-known/mercure}
              ###> dunglas/symfony-docker ###
              # Next lines are only used during initial installation
              SYMFONY_VERSION: ${SYMFONY_VERSION:-}
              ###< dunglas/symfony-docker ###
            volumes:
              - caddy_data:/data

        # Mercure is installed as a Caddy module, prevent the Flex recipe from installing another service
        ###> symfony/mercure-bundle ###
        ###< symfony/mercure-bundle ###

        volumes:
          caddy_data:
        YAML;

    private string $apiDir;
    private Filesystem $fs;

    protected function setUp(): void
    {
        $this->fs = new Filesystem();
        $this->apiDir = sys_get_temp_dir().'/compose-override-'.bin2hex(random_bytes(4));
        $this->fs->mkdir($this->apiDir);
    }

    protected function tearDown(): void
    {
        $this->fs->remove($this->apiDir);
    }

    public function testInjectsCaddyDirectivesUnderPhpEnvironmentBetweenMarkers(): void
    {
        file_put_contents($this->apiDir.'/compose.yaml', self::UPSTREAM_COMPOSE);

        (new ComposeOverrideWriter())->write($this->apiDir);

        $patched = (string) file_get_contents($this->apiDir.'/compose.yaml');
        $this->assertMatchesRegularExpression(
            '/^      ###> api-platform\/api-platform ###\n      CADDY_SERVER_EXTRA_DIRECTIVES: .*\n      ###< api-platform\/api-platform ###$/m',
            $patched,
            'must wrap the injected key in Flex-style markers at environment indent',
        );
        $this->assertStringContainsString(ComposeOverrideWriter::CADDY_DIRECTIVES, $patched);
    }

    public function testPreservesUpstreamCommentsAndOtherMarkers(): void
    {
        file_put_contents($this->apiDir.'/compose.yaml', self::UPSTREAM_COMPOSE);

        (new ComposeOverrideWriter())->write($this->apiDir);

        $patched = (string) file_get_contents($this->apiDir.'/compose.yaml');
        $this->assertStringContainsString('# Run "composer require symfony/orm-pack"', $patched);
        $this->assertStringContainsString('# Run "composer require symfony/mercure-bundle"', $patched);
        $this->assertStringContainsString('# Mercure is installed as a Caddy module', $patched);
        $this->assertStringContainsString('# Next lines are only used during initial installation', $patched);
        $this->assertStringContainsString('###> dunglas/symfony-docker ###', $patched);
        $this->assertStringContainsString('###> symfony/mercure-bundle ###', $patched);
    }

    public function testInjectionLandsBeforeVolumesSiblingNotInside(): void
    {
        file_put_contents($this->apiDir.'/compose.yaml', self::UPSTREAM_COMPOSE);

        (new ComposeOverrideWriter())->write($this->apiDir);

        $patched = (string) file_get_contents($this->apiDir.'/compose.yaml');
        $caddyPos = strpos($patched, 'CADDY_SERVER_EXTRA_DIRECTIVES');
        $volumesPos = strpos($patched, "\n    volumes:");
        $this->assertNotFalse($caddyPos);
        $this->assertNotFalse($volumesPos);
        $this->assertLessThan($volumesPos, $caddyPos, 'injected key must sit inside environment, before sibling volumes:');
    }

    public function testDoesNotCreateSidecarComposeFile(): void
    {
        file_put_contents($this->apiDir.'/compose.yaml', self::UPSTREAM_COMPOSE);

        (new ComposeOverrideWriter())->write($this->apiDir);

        $this->assertFileDoesNotExist($this->apiDir.'/compose.api-platform.yaml');
    }

    public function testDoesNotModifyEnvFile(): void
    {
        file_put_contents($this->apiDir.'/compose.yaml', self::UPSTREAM_COMPOSE);
        file_put_contents($this->apiDir.'/.env', "APP_ENV=dev\n");

        (new ComposeOverrideWriter())->write($this->apiDir);

        $this->assertSame("APP_ENV=dev\n", (string) file_get_contents($this->apiDir.'/.env'));
    }

    public function testFailsLoudlyWhenComposeFileIsMissing(): void
    {
        $this->expectException(\RuntimeException::class);
        (new ComposeOverrideWriter())->write($this->apiDir);
    }

    public function testFailsLoudlyWhenServicesPhpEnvironmentIsMissing(): void
    {
        file_put_contents($this->apiDir.'/compose.yaml', "services:\n  database:\n    image: postgres\n");

        $this->expectException(\RuntimeException::class);
        (new ComposeOverrideWriter())->write($this->apiDir);
    }

    public function testIsIdempotentOnRepeatedRuns(): void
    {
        file_put_contents($this->apiDir.'/compose.yaml', self::UPSTREAM_COMPOSE);

        $writer = new ComposeOverrideWriter();
        $writer->write($this->apiDir);
        $first = (string) file_get_contents($this->apiDir.'/compose.yaml');
        $writer->write($this->apiDir);
        $second = (string) file_get_contents($this->apiDir.'/compose.yaml');

        $this->assertSame($first, $second);
        $this->assertSame(1, substr_count($second, '###> api-platform/api-platform ###'));
        $this->assertSame(1, substr_count($second, 'CADDY_SERVER_EXTRA_DIRECTIVES:'));
    }

    public function testInjectsUnderFrankenphpServiceName(): void
    {
        // Upstream symfony-docker may rename the `php` service to
        // `frankenphp` (matching the image name); the writer must keep
        // finding the `environment` block under the new key.
        $compose = <<<'YAML'
            services:
              frankenphp:
                restart: unless-stopped
                environment:
                  SERVER_NAME: ${SERVER_NAME:-localhost}, php:80
                volumes:
                  - caddy_data:/data

            volumes:
              caddy_data:
            YAML;
        file_put_contents($this->apiDir.'/compose.yaml', $compose);

        (new ComposeOverrideWriter())->write($this->apiDir);

        $patched = (string) file_get_contents($this->apiDir.'/compose.yaml');
        $this->assertStringContainsString(ComposeOverrideWriter::CADDY_DIRECTIVES, $patched);
        $this->assertMatchesRegularExpression('/^      ###> api-platform\/api-platform ###$/m', $patched);
    }

    public function testRewritesBlockInPlaceWhenDirectivesChange(): void
    {
        file_put_contents($this->apiDir.'/compose.yaml', self::UPSTREAM_COMPOSE);

        $writer = new ComposeOverrideWriter();
        $writer->write($this->apiDir);

        // Simulate an upstream installer upgrade landing a different value.
        $tampered = str_replace(
            ComposeOverrideWriter::CADDY_DIRECTIVES,
            'header X-Old-Value 1',
            (string) file_get_contents($this->apiDir.'/compose.yaml'),
        );
        file_put_contents($this->apiDir.'/compose.yaml', $tampered);

        $writer->write($this->apiDir);
        $patched = (string) file_get_contents($this->apiDir.'/compose.yaml');

        $this->assertSame(1, substr_count($patched, '###> api-platform/api-platform ###'));
        $this->assertStringContainsString(ComposeOverrideWriter::CADDY_DIRECTIVES, $patched);
        $this->assertStringNotContainsString('X-Old-Value', $patched);
    }
}
