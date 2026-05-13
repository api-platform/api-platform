<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Tests\Scaffold;

use ApiPlatform\Installer\Scaffold\ComposeOverrideWriter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

final class ComposeOverrideWriterTest extends TestCase
{
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

    public function testWritesOverrideFileAndAppendsComposeFileToEnv(): void
    {
        file_put_contents($this->apiDir.'/.env', "APP_ENV=dev\n");

        (new ComposeOverrideWriter())->write($this->apiDir);

        $yaml = Yaml::parseFile($this->apiDir.'/compose.api-platform.yaml');
        $this->assertSame(
            ComposeOverrideWriter::CADDY_DIRECTIVES,
            $yaml['services']['php']['environment']['CADDY_SERVER_EXTRA_DIRECTIVES'],
        );

        $env = (string) file_get_contents($this->apiDir.'/.env');
        $this->assertStringContainsString('APP_ENV=dev', $env, 'must keep prior .env content');
        $this->assertStringContainsString('COMPOSE_FILE=compose.yaml:compose.override.yaml:compose.api-platform.yaml', $env);
    }

    public function testFailsLoudlyWhenEnvIsMissing(): void
    {
        $this->expectException(\RuntimeException::class);
        (new ComposeOverrideWriter())->write($this->apiDir);
    }

    public function testIsIdempotentAndDoesNotDuplicateComposeFileLine(): void
    {
        file_put_contents($this->apiDir.'/.env', "APP_ENV=dev\n");

        $writer = new ComposeOverrideWriter();
        $writer->write($this->apiDir);
        $writer->write($this->apiDir);

        $env = (string) file_get_contents($this->apiDir.'/.env');
        $this->assertSame(1, substr_count($env, 'COMPOSE_FILE=compose.yaml:compose.override.yaml:compose.api-platform.yaml'));
    }
}
