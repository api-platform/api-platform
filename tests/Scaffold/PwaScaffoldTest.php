<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Tests\Scaffold;

use ApiPlatform\Installer\Scaffold\PwaScaffold;
use PHPUnit\Framework\TestCase;

final class PwaScaffoldTest extends TestCase
{
    public function testKeepsExistingCorsAllowOriginFromNelmioRecipe(): void
    {
        $env = <<<'ENV'
            APP_ENV=dev

            ###> nelmio/cors-bundle ###
            CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
            ###< nelmio/cors-bundle ###
            ENV;

        $this->assertSame($env, PwaScaffold::patchCorsAllowOriginEnv($env));
    }

    public function testAddsNelmioCorsAllowOriginFallbackWhenMissing(): void
    {
        $patched = PwaScaffold::patchCorsAllowOriginEnv("APP_ENV=dev\n");

        $this->assertStringContainsString(
            "CORS_ALLOW_ORIGIN='^https?://(localhost|127\\.0\\.0\\.1)(:[0-9]+)?$'\n",
            $patched,
        );
    }

    public function testCorsAllowOriginFallbackIsIdempotent(): void
    {
        $once = PwaScaffold::patchCorsAllowOriginEnv("APP_ENV=dev\n");

        $this->assertSame($once, PwaScaffold::patchCorsAllowOriginEnv($once));
        $this->assertSame(1, substr_count($once, 'CORS_ALLOW_ORIGIN='));
    }

    public function testApprovesPnpmBuildPlaceholdersCreatedByCreateNextApp(): void
    {
        $patched = PwaScaffold::approvePnpmWorkspaceBuilds(<<<'YAML'
            allowBuilds:
              sharp: set this to true or false
              unrs-resolver: set this to true or false
            ignoredBuiltDependencies:
              - sharp
              - unrs-resolver
            YAML);

        $this->assertStringContainsString("sharp: true\n", $patched);
        $this->assertStringContainsString("unrs-resolver: true\n", $patched);
        $this->assertStringNotContainsString('set this to true or false', $patched);
        $this->assertStringNotContainsString('ignoredBuiltDependencies', $patched);
    }

    public function testKeepsUnrelatedIgnoredBuildDependencies(): void
    {
        $patched = PwaScaffold::approvePnpmWorkspaceBuilds(<<<'YAML'
            allowBuilds:
              sharp: set this to true or false
              unrs-resolver: set this to true or false
            ignoredBuiltDependencies:
              - sharp
              - fsevents
              - unrs-resolver
            YAML);

        $this->assertStringContainsString("sharp: true\n", $patched);
        $this->assertStringContainsString("unrs-resolver: true\n", $patched);
        $this->assertStringContainsString("ignoredBuiltDependencies:\n", $patched);
        $this->assertStringContainsString("- fsevents\n", $patched);
        $this->assertStringNotContainsString("- sharp\n", $patched);
        $this->assertStringNotContainsString("- unrs-resolver\n", $patched);
    }

    public function testDoesNotOverwriteExplicitPnpmBuildDecisions(): void
    {
        $workspace = <<<'YAML'
            allowBuilds:
              sharp: false
              unrs-resolver: true
            ignoredBuiltDependencies:
              - sharp
            YAML;

        $this->assertSame($workspace, PwaScaffold::approvePnpmWorkspaceBuilds($workspace));
    }

    public function testPnpmBuildApprovalPatchIsIdempotent(): void
    {
        $once = PwaScaffold::approvePnpmWorkspaceBuilds(<<<'YAML'
            allowBuilds:
              sharp: set this to true or false
              unrs-resolver: set this to true or false
            ignoredBuiltDependencies:
              - sharp
              - unrs-resolver
            YAML);

        $this->assertSame($once, PwaScaffold::approvePnpmWorkspaceBuilds($once));
    }
}
