<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Tests\Scaffold;

use ApiPlatform\Installer\Scaffold\ScaffoldOptions;
use ApiPlatform\Installer\Scaffold\SymfonyScaffold;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SymfonyScaffoldTest extends TestCase
{
    public function testEnablesSelectedDocsAndDisablesOthers(): void
    {
        $config = SymfonyScaffold::buildApiPlatformConfig(new ScaffoldOptions(
            withPwa: false,
            withDocker: false,
            formats: ['jsonld'],
            docs: ['scalar'],
        ));

        $this->assertFalse($config['enable_swagger_ui']);
        $this->assertFalse($config['enable_re_doc']);
        $this->assertTrue($config['enable_scalar']);
    }

    public function testDisablesScalarWhenNotSelected(): void
    {
        $config = SymfonyScaffold::buildApiPlatformConfig(new ScaffoldOptions(
            withPwa: false,
            withDocker: false,
            formats: ['jsonld'],
            docs: ['swagger_ui'],
        ));

        $this->assertTrue($config['enable_swagger_ui']);
        $this->assertFalse($config['enable_scalar']);
    }

    public function testSymfonyDockerRefIsPinnedToFullCommitSha(): void
    {
        // A floating ref (branch name) means every install would track upstream
        // HEAD; pinning to a 40-char SHA-1 guarantees reproducible scaffolds.
        $this->assertMatchesRegularExpression('/^[0-9a-f]{40}$/', SymfonyScaffold::SYMFONY_DOCKER_REF);
    }

    public function testDisablesAllUiViewersWhenEmptyButKeepsHydraDocs(): void
    {
        // No UI selected must NOT trigger enable_docs: false — that's a master
        // kill switch in core that sets hideHydraOperation: true and empties
        // /docs.jsonld's supportedClass, breaking Hydra clients like the PWA.
        $config = SymfonyScaffold::buildApiPlatformConfig(new ScaffoldOptions(
            withPwa: false,
            withDocker: false,
            formats: ['jsonld'],
            docs: [],
        ));

        $this->assertFalse($config['enable_swagger_ui']);
        $this->assertFalse($config['enable_re_doc']);
        $this->assertFalse($config['enable_scalar']);
        $this->assertArrayNotHasKey('enable_docs', $config, 'must not set enable_docs at all — defaults to true in core');
        $this->assertArrayNotHasKey('html', $config['formats'], 'no HTML viewer means no html format');
    }

    public function testAdminOnlyCorsFallbackUsesNelmioRecipeValue(): void
    {
        $apiDir = sys_get_temp_dir().'/symfony-scaffold-test-'.bin2hex(random_bytes(4));
        mkdir($apiDir);
        file_put_contents($apiDir.'/composer.json', '{"require":{"nelmio/cors-bundle":"^2.5"}}');
        file_put_contents($apiDir.'/.env', "APP_ENV=dev\n");

        try {
            $scaffold = new SymfonyScaffold(new SymfonyStyle(new ArrayInput([]), new BufferedOutput()));
            $method = new \ReflectionMethod($scaffold, 'setupCorsForLocalhost');
            $method->invoke($scaffold, $apiDir);

            $env = (string) file_get_contents($apiDir.'/.env');
            $this->assertStringContainsString("CORS_ALLOW_ORIGIN='^https?://(localhost|127\\.0\\.0\\.1)(:[0-9]+)?$'\n", $env);
            $this->assertSame(1, substr_count($env, 'CORS_ALLOW_ORIGIN='));
        } finally {
            @unlink($apiDir.'/.env');
            @unlink($apiDir.'/composer.json');
            @rmdir($apiDir);
        }
    }
}
