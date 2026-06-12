<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

use ApiPlatform\Installer\Templates;
use RuntimeException;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Scaffolds the React-admin SPA into a Laravel project by reusing Laravel's
 * existing Vite setup: drops Vite entries under `resources/js/admin/`, adds
 * a Blade view, patches `vite.config.js` to register the new input and
 * `routes/web.php` to serve `/admin`, then installs the npm deps.
 */
final class LaravelAdminScaffold
{
    public const ROUTE_START_MARKER = '// ###> '.AdminRecipe::NAME.' ###';
    public const ROUTE_END_MARKER = '// ###< '.AdminRecipe::NAME.' ###';

    private const JS_PACKAGES = [
        '@api-platform/admin',
        'react',
        'react-admin',
        'react-dom',
    ];

    private readonly Filesystem $fs;
    private readonly ProcessRunner $runner;
    private readonly ViteConfigPatcher $vitePatcher;

    public function __construct(
        private readonly SymfonyStyle $io,
    ) {
        $this->fs = new Filesystem();
        $this->runner = new ProcessRunner($io);
        $this->vitePatcher = new ViteConfigPatcher();
    }

    public function run(string $projectDir): void
    {
        $this->io->writeln('<info>Installing React-admin SPA</info>');

        $adminJsDir = $projectDir.'/resources/js/admin';
        $this->fs->mkdir($adminJsDir);
        $this->fs->copy(Templates::path('admin-app.tsx'), $adminJsDir.'/App.tsx', true);
        $this->fs->copy(Templates::path('admin-main.tsx'), $adminJsDir.'/main.tsx', true);

        $this->fs->copy(
            Templates::path('laravel-admin.blade.php'),
            $projectDir.'/resources/views/admin.blade.php',
            true,
        );

        $this->patchViteConfig($projectDir);
        $this->patchRoutes($projectDir);
        $this->patchEnv($projectDir);

        $missing = self::filterInstalledPackages($projectDir, self::JS_PACKAGES);
        if ([] !== $missing) {
            $this->io->writeln('<info>Installing admin JavaScript dependencies</info>');
            $this->runner->run(['npm', 'install', ...$missing], $projectDir);
        }
    }

    /**
     * Adds `VITE_ENTRYPOINT=...` to the project `.env` so the admin app
     * (served by Laravel at /admin) reaches the API under its route_prefix.
     * Idempotent: a custom value is left untouched on re-run.
     */
    public static function appendViteEntrypointEnv(string $existing, string $entrypoint): string
    {
        if (preg_match('/^VITE_ENTRYPOINT=/m', $existing)) {
            return $existing;
        }

        $sep = '' === $existing || str_ends_with($existing, "\n") ? '' : "\n";

        return $existing.$sep.'VITE_ENTRYPOINT='.$entrypoint."\n";
    }

    /**
     * @param array<string> $packages
     *
     * @return array<string>
     */
    public static function filterInstalledPackages(string $projectDir, array $packages): array
    {
        $manifest = $projectDir.'/package.json';
        if (!is_file($manifest)) {
            return $packages;
        }
        $json = json_decode((string) file_get_contents($manifest), true);
        if (!\is_array($json)) {
            return $packages;
        }
        $installed = array_merge(
            \is_array($json['dependencies'] ?? null) ? array_keys($json['dependencies']) : [],
            \is_array($json['devDependencies'] ?? null) ? array_keys($json['devDependencies']) : [],
        );

        return array_values(array_diff($packages, $installed));
    }

    /**
     * Appends the `/admin/{path?}` route between Flex-style markers. Idempotent.
     */
    public static function appendAdminRoute(string $routes): string
    {
        if (str_contains($routes, self::ROUTE_START_MARKER)) {
            return $routes;
        }

        $prefix = match (true) {
            '' === $routes, str_ends_with($routes, "\n\n") => '',
            str_ends_with($routes, "\n") => "\n",
            default => "\n\n",
        };
        $block = self::ROUTE_START_MARKER."\n"
            ."Route::get('/admin/{path?}', fn () => view('admin'))->where('path', '.*');\n"
            .self::ROUTE_END_MARKER."\n";

        return $routes.$prefix.$block;
    }

    private function patchViteConfig(string $projectDir): void
    {
        $viteConfig = $projectDir.'/vite.config.js';
        if (!is_file($viteConfig)) {
            throw new RuntimeException(sprintf('Could not find %s.', $viteConfig));
        }
        $patched = $this->vitePatcher->patch(
            (string) file_get_contents($viteConfig),
            'resources/js/admin/main.tsx',
        );
        FileWriter::write($viteConfig, $patched);
    }

    private function patchRoutes(string $projectDir): void
    {
        $routesFile = $projectDir.'/routes/web.php';
        if (!is_file($routesFile)) {
            throw new RuntimeException(sprintf('Could not find %s.', $routesFile));
        }
        $existing = (string) file_get_contents($routesFile);
        $patched = self::appendAdminRoute($existing);
        if ($patched !== $existing) {
            FileWriter::write($routesFile, $patched);
        }
    }

    private function patchEnv(string $projectDir): void
    {
        $envFile = $projectDir.'/.env';
        if (!is_file($envFile)) {
            return;
        }
        $existing = (string) file_get_contents($envFile);
        $patched = self::appendViteEntrypointEnv($existing, '/api');
        if ($patched !== $existing) {
            FileWriter::write($envFile, $patched);
        }
    }
}
