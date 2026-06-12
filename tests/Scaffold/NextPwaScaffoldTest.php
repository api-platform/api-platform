<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Tests\Scaffold;

use ApiPlatform\Installer\Scaffold\NextPwaScaffold;
use PHPUnit\Framework\TestCase;

final class NextPwaScaffoldTest extends TestCase
{
    public function testKeepsExistingCorsAllowOriginFromNelmioRecipe(): void
    {
        $env = <<<'ENV'
            APP_ENV=dev

            ###> nelmio/cors-bundle ###
            CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
            ###< nelmio/cors-bundle ###
            ENV;

        $this->assertSame($env, NextPwaScaffold::patchCorsAllowOriginEnv($env));
    }

    public function testAddsNelmioCorsAllowOriginFallbackWhenMissing(): void
    {
        $patched = NextPwaScaffold::patchCorsAllowOriginEnv("APP_ENV=dev\n");

        $this->assertStringContainsString(
            "CORS_ALLOW_ORIGIN='^https?://(localhost|127\\.0\\.0\\.1)(:[0-9]+)?$'\n",
            $patched,
        );
    }

    public function testCorsAllowOriginFallbackIsIdempotent(): void
    {
        $once = NextPwaScaffold::patchCorsAllowOriginEnv("APP_ENV=dev\n");

        $this->assertSame($once, NextPwaScaffold::patchCorsAllowOriginEnv($once));
        $this->assertSame(1, substr_count($once, 'CORS_ALLOW_ORIGIN='));
    }

    public function testApprovesPnpmBuildPlaceholdersCreatedByCreateNextApp(): void
    {
        $patched = NextPwaScaffold::approvePnpmWorkspaceBuilds(<<<'YAML'
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
        $patched = NextPwaScaffold::approvePnpmWorkspaceBuilds(<<<'YAML'
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

        $this->assertSame($workspace, NextPwaScaffold::approvePnpmWorkspaceBuilds($workspace));
    }

    public function testPnpmBuildApprovalPatchIsIdempotent(): void
    {
        $once = NextPwaScaffold::approvePnpmWorkspaceBuilds(<<<'YAML'
            allowBuilds:
              sharp: set this to true or false
              unrs-resolver: set this to true or false
            ignoredBuiltDependencies:
              - sharp
              - unrs-resolver
            YAML);

        $this->assertSame($once, NextPwaScaffold::approvePnpmWorkspaceBuilds($once));
    }

    public function testWritesNextPublicApiEntrypointEnvWhenAbsent(): void
    {
        $patched = NextPwaScaffold::appendNextPublicApiEntrypointEnv('', 'http://localhost:8000');

        $this->assertStringContainsString('NEXT_PUBLIC_API_ENTRYPOINT=http://localhost:8000', $patched);
    }

    public function testNextPublicApiEntrypointEnvIsIdempotent(): void
    {
        $once = NextPwaScaffold::appendNextPublicApiEntrypointEnv('', 'http://localhost:8000');
        $twice = NextPwaScaffold::appendNextPublicApiEntrypointEnv($once, 'http://localhost:8000');

        $this->assertSame($once, $twice);
        $this->assertSame(1, substr_count($twice, 'NEXT_PUBLIC_API_ENTRYPOINT='));
    }

    public function testNextPublicApiEntrypointEnvDoesNotOverwriteCustomValue(): void
    {
        $existing = "NEXT_PUBLIC_API_ENTRYPOINT=https://api.example.com\n";
        $patched = NextPwaScaffold::appendNextPublicApiEntrypointEnv($existing, 'http://localhost:8000');

        $this->assertSame($existing, $patched);
    }

    public function testCreateNextAppCommandPinsTypeScriptAndAppRouter(): void
    {
        $cmd = NextPwaScaffold::createNextAppCommand('pwa');

        $this->assertContains('--use-pnpm', $cmd);
        // --yes skips every interactive prompt so the installer never stalls
        // on a TTY waiting for choices the user can change in the template.
        $this->assertContains('--yes', $cmd);
        // NextPwaScaffold relies on the App Router layout under app/page.tsx;
        // a Pages-router or JS scaffold would crash the entry-page lookup.
        $this->assertContains('--ts', $cmd);
        $this->assertContains('--app', $cmd);
    }
}
