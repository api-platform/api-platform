<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Tests\Scaffold;

use ApiPlatform\Installer\Scaffold\SymfonyAdminScaffold;
use ApiPlatform\Installer\Templates;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SymfonyAdminScaffoldTest extends TestCase
{
    public function testAppendsViteEntrypointEnvWhenAbsent(): void
    {
        $patched = SymfonyAdminScaffold::appendEntrypointEnv('', 'https://localhost');

        $this->assertStringContainsString('VITE_ENTRYPOINT=https://localhost', $patched);
    }

    public function testEntrypointEnvAppendIsIdempotent(): void
    {
        $once = SymfonyAdminScaffold::appendEntrypointEnv('', 'https://localhost');
        $twice = SymfonyAdminScaffold::appendEntrypointEnv($once, 'https://localhost');

        $this->assertSame($once, $twice);
        $this->assertSame(1, substr_count($twice, 'VITE_ENTRYPOINT='));
    }

    public function testEntrypointEnvDoesNotOverwriteCustomValue(): void
    {
        $existing = "VITE_ENTRYPOINT=https://my.api.example.com\n";
        $patched = SymfonyAdminScaffold::appendEntrypointEnv($existing, 'https://localhost');

        $this->assertSame($existing, $patched);
    }

    public function testAdminTemplatesAreShippedWithTheInstaller(): void
    {
        $this->assertFileExists(Templates::path('admin-app.tsx'));
        $this->assertFileExists(Templates::path('admin-main.tsx'));
        $this->assertFileExists(Templates::path('admin-index.html'));
        $this->assertFileExists(Templates::path('admin-package.json'));
        $this->assertFileExists(Templates::path('admin-vite.config.ts'));
        $this->assertFileExists(Templates::path('admin-tsconfig.json'));
    }

    public function testAdminPackageJsonPinsCriticalDeps(): void
    {
        $content = (string) file_get_contents(Templates::path('admin-package.json'));
        $pkg = json_decode($content, true, flags: \JSON_THROW_ON_ERROR);

        $this->assertIsArray($pkg);
        $this->assertArrayHasKey('@api-platform/admin', $pkg['dependencies']);
        $this->assertArrayHasKey('react', $pkg['dependencies']);
        $this->assertArrayHasKey('react-dom', $pkg['dependencies']);
        $this->assertArrayHasKey('react-admin', $pkg['dependencies']);
        $this->assertArrayHasKey('vite', $pkg['devDependencies']);
        $this->assertArrayHasKey('@vitejs/plugin-react', $pkg['devDependencies']);
    }

    public function testAdminIndexHtmlPointsAtMainEntry(): void
    {
        $content = (string) file_get_contents(Templates::path('admin-index.html'));

        $this->assertStringContainsString('id="admin-root"', $content);
        $this->assertStringContainsString('src="/src/main.tsx"', $content);
    }

    public function testPrintsOverrideHintForChosenEntrypoint(): void
    {
        // VITE_ENTRYPOINT is frozen by appendEntrypointEnv on first run; if
        // the user's actual Symfony server port differs from the heuristic,
        // they need an obvious paper-trail back to admin/.env.
        $output = new BufferedOutput();
        $io = new SymfonyStyle(new ArrayInput([]), $output);
        $scaffold = new SymfonyAdminScaffold($io);
        $method = new \ReflectionMethod($scaffold, 'printEntrypointHint');

        $method->invoke($scaffold, 'http://localhost:8000');

        $display = $output->fetch();
        $this->assertStringContainsString('admin/.env', $display);
        $this->assertStringContainsString('VITE_ENTRYPOINT', $display);
        $this->assertStringContainsString('http://localhost:8000', $display);
    }
}
