<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Tests\Scaffold;

use ApiPlatform\Installer\Scaffold\ViteConfigPatcher;
use PHPUnit\Framework\TestCase;

final class ViteConfigPatcherTest extends TestCase
{
    private const LARAVEL_VITE_CONFIG = <<<'JS'
        import { defineConfig } from 'vite';
        import laravel from 'laravel-vite-plugin';

        export default defineConfig({
            plugins: [
                laravel({
                    input: ['resources/css/app.css', 'resources/js/app.js'],
                    refresh: true,
                }),
            ],
        });

        JS;

    public function testAddsEntryToInputArrayBetweenMarkers(): void
    {
        $patched = (new ViteConfigPatcher())->patch(self::LARAVEL_VITE_CONFIG, 'resources/js/admin/main.tsx');

        $this->assertStringContainsString('/* ###> api-platform/admin ### */', $patched);
        $this->assertStringContainsString('/* ###< api-platform/admin ### */', $patched);
        $this->assertStringContainsString("'resources/js/admin/main.tsx'", $patched);
    }

    public function testPreservesExistingInputsAndConfigKeys(): void
    {
        $patched = (new ViteConfigPatcher())->patch(self::LARAVEL_VITE_CONFIG, 'resources/js/admin/main.tsx');

        $this->assertStringContainsString("'resources/css/app.css'", $patched);
        $this->assertStringContainsString("'resources/js/app.js'", $patched);
        $this->assertStringContainsString('refresh: true', $patched);
        $this->assertStringContainsString("import laravel from 'laravel-vite-plugin'", $patched);
    }

    public function testNewEntryLandsInsideInputArrayBeforeClosingBracket(): void
    {
        $patched = (new ViteConfigPatcher())->patch(self::LARAVEL_VITE_CONFIG, 'resources/js/admin/main.tsx');

        $entryPos = strpos($patched, "'resources/js/admin/main.tsx'");
        $closePos = strpos($patched, '],');
        $this->assertNotFalse($entryPos);
        $this->assertNotFalse($closePos);
        $this->assertLessThan($closePos, $entryPos, 'new entry must sit inside the input array');
    }

    public function testIsIdempotentOnRepeatedPatches(): void
    {
        $patcher = new ViteConfigPatcher();
        $once = $patcher->patch(self::LARAVEL_VITE_CONFIG, 'resources/js/admin/main.tsx');
        $twice = $patcher->patch($once, 'resources/js/admin/main.tsx');

        $this->assertSame($once, $twice);
        $this->assertSame(1, substr_count($twice, '/* ###> api-platform/admin ### */'));
        $this->assertSame(1, substr_count($twice, "'resources/js/admin/main.tsx'"));
    }

    public function testFailsLoudlyWhenInputArrayMissing(): void
    {
        $this->expectException(\RuntimeException::class);

        (new ViteConfigPatcher())->patch("export default {};\n", 'resources/js/admin/main.tsx');
    }
}
