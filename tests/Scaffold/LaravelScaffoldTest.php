<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Tests\Scaffold;

use ApiPlatform\Installer\Scaffold\LaravelScaffold;
use ApiPlatform\Installer\Templates;
use PHPUnit\Framework\TestCase;

final class LaravelScaffoldTest extends TestCase
{
    public function testAppendsImportWhenAbsent(): void
    {
        $existing = "import './bootstrap';\n";
        $patched = LaravelScaffold::appendAppJsImport($existing, './api-platform-resources');

        $this->assertStringContainsString("import './bootstrap';", $patched);
        $this->assertStringContainsString("import './api-platform-resources';", $patched);
    }

    public function testAppendIsIdempotent(): void
    {
        $existing = "import './bootstrap';\n";
        $once = LaravelScaffold::appendAppJsImport($existing, './api-platform-resources');
        $twice = LaravelScaffold::appendAppJsImport($once, './api-platform-resources');

        $this->assertSame($once, $twice);
        $this->assertSame(1, substr_count($twice, "import './api-platform-resources';"));
    }

    public function testWelcomeTemplateExistsAndUsesViteAndMountPoint(): void
    {
        $path = Templates::path('laravel-welcome.blade.php');
        $this->assertFileExists($path);
        $content = (string) file_get_contents($path);

        $this->assertStringContainsString("@vite(", $content, 'must wire Vite-bundled assets');
        $this->assertStringContainsString('id="ap-resources"', $content, 'must include the mount point for live resource rendering');
        $this->assertStringContainsString('API Platform', $content);
    }

    public function testResourcesTemplateUsesHydraParserAgainstLaravelEntrypoint(): void
    {
        $path = Templates::path('laravel-resources.js');
        $this->assertFileExists($path);
        $content = (string) file_get_contents($path);

        $this->assertStringContainsString('@api-platform/api-doc-parser', $content);
        $this->assertStringContainsString('parseHydraDocumentation', $content);
        $this->assertStringContainsString('/api', $content, 'must target the Laravel default route_prefix');
        $this->assertStringContainsString('window.location.origin', $content, 'jsonld.expand needs an absolute base URL');
    }

    public function testPatchesDefaultAppUrl(): void
    {
        $env = "APP_NAME=Laravel\nAPP_URL=http://localhost\nDB_CONNECTION=sqlite\n";
        $patched = LaravelScaffold::patchAppUrlEnv($env, 'http://localhost:8000');

        $this->assertStringContainsString('APP_URL=http://localhost:8000', $patched);
        $this->assertStringContainsString('APP_NAME=Laravel', $patched);
        $this->assertStringContainsString('DB_CONNECTION=sqlite', $patched);
    }

    public function testLeavesCustomAppUrlUntouched(): void
    {
        $env = "APP_URL=https://api.example.com\n";
        $patched = LaravelScaffold::patchAppUrlEnv($env, 'http://localhost:8000');

        $this->assertSame($env, $patched);
    }
}
