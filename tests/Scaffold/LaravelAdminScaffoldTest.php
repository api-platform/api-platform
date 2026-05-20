<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Tests\Scaffold;

use ApiPlatform\Installer\Scaffold\LaravelAdminScaffold;
use ApiPlatform\Installer\Templates;
use PHPUnit\Framework\TestCase;

final class LaravelAdminScaffoldTest extends TestCase
{
    public function testAppendsAdminRouteBetweenMarkersWhenAbsent(): void
    {
        $routes = "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\nRoute::get('/', function () { return view('welcome'); });\n";
        $patched = LaravelAdminScaffold::appendAdminRoute($routes);

        $this->assertStringContainsString('// ###> api-platform/admin ###', $patched);
        $this->assertStringContainsString('// ###< api-platform/admin ###', $patched);
        $this->assertStringContainsString("Route::get('/admin/{path?}'", $patched);
        $this->assertStringContainsString("->where('path', '.*')", $patched);
        $this->assertStringContainsString("Route::get('/', function () { return view('welcome'); });", $patched);
    }

    public function testAdminRouteAppendIsIdempotent(): void
    {
        $routes = "<?php\n\nRoute::get('/', fn () => view('welcome'));\n";
        $once = LaravelAdminScaffold::appendAdminRoute($routes);
        $twice = LaravelAdminScaffold::appendAdminRoute($once);

        $this->assertSame($once, $twice);
        $this->assertSame(1, substr_count($twice, '// ###> api-platform/admin ###'));
        $this->assertSame(1, substr_count($twice, "Route::get('/admin/{path?}'"));
    }

    public function testAdminBladeTemplateExistsAndPointsAtViteEntry(): void
    {
        $path = Templates::path('laravel-admin.blade.php');
        $this->assertFileExists($path);
        $content = (string) file_get_contents($path);

        $this->assertStringContainsString("@vite(['resources/js/admin/main.tsx'])", $content);
        $this->assertStringContainsString('id="admin-root"', $content);
    }

    public function testAdminAppTemplateUsesHydraAdminAndDefaultsToWindowOrigin(): void
    {
        $path = Templates::path('admin-app.tsx');
        $this->assertFileExists($path);
        $content = (string) file_get_contents($path);

        $this->assertStringContainsString('@api-platform/admin', $content);
        $this->assertStringContainsString('HydraAdmin', $content);
        $this->assertStringContainsString('window.location.origin', $content);
    }

    public function testAdminMainTemplateMountsOnAdminRoot(): void
    {
        $path = Templates::path('admin-main.tsx');
        $this->assertFileExists($path);
        $content = (string) file_get_contents($path);

        $this->assertStringContainsString("getElementById('admin-root')", $content);
        $this->assertStringContainsString('createRoot', $content);
        $this->assertStringContainsString("from './App'", $content);
    }

    public function testFilterInstalledPackagesSkipsAlreadyDeclaredDeps(): void
    {
        $projectDir = sys_get_temp_dir().'/laravel-admin-'.bin2hex(random_bytes(4));
        mkdir($projectDir);
        try {
            file_put_contents(
                $projectDir.'/package.json',
                json_encode([
                    'dependencies' => ['react' => '^18.3.0'],
                    'devDependencies' => ['@vitejs/plugin-react' => '^4.3.0'],
                ], \JSON_THROW_ON_ERROR),
            );

            $missing = LaravelAdminScaffold::filterInstalledPackages(
                $projectDir,
                ['@api-platform/admin', 'react', 'react-dom', '@vitejs/plugin-react'],
            );

            $this->assertSame(['@api-platform/admin', 'react-dom'], $missing);
        } finally {
            unlink($projectDir.'/package.json');
            rmdir($projectDir);
        }
    }

    public function testFilterInstalledPackagesReturnsAllWhenManifestMissing(): void
    {
        $projectDir = sys_get_temp_dir().'/laravel-admin-'.bin2hex(random_bytes(4));
        mkdir($projectDir);
        try {
            $packages = ['@api-platform/admin', 'react'];
            $this->assertSame($packages, LaravelAdminScaffold::filterInstalledPackages($projectDir, $packages));
        } finally {
            rmdir($projectDir);
        }
    }
}
