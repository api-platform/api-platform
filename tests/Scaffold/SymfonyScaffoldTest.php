<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Tests\Scaffold;

use ApiPlatform\Installer\Scaffold\ScaffoldOptions;
use ApiPlatform\Installer\Scaffold\SymfonyScaffold;
use PHPUnit\Framework\TestCase;

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
}
