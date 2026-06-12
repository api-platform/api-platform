<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

use ApiPlatform\Installer\Templates;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ExecutableFinder;

final class LaravelScaffold
{
    private const JS_PACKAGES = [
        '@api-platform/api-doc-parser',
        'github:api-platform/zod',
        '@api-platform/ld',
        '@api-platform/mercure',
    ];

    private readonly ProcessRunner $runner;
    private readonly LaravelConfigPatcher $patcher;
    private readonly Filesystem $fs;

    public function __construct(
        private readonly SymfonyStyle $io,
    ) {
        $this->runner = new ProcessRunner($io);
        $this->patcher = new LaravelConfigPatcher();
        $this->fs = new Filesystem();
    }

    public function run(string $projectDir, string $projectName, ScaffoldOptions $opts): int
    {
        $finder = new ExecutableFinder();
        $missing = array_values(array_filter(['composer', 'php', 'npm'], static fn (string $b): bool => null === $finder->find($b)));
        if ([] !== $missing) {
            throw new RuntimeException(sprintf('Missing required binaries in PATH: %s.', implode(', ', $missing)));
        }

        $parentDir = \dirname($projectDir);

        if (null !== $finder->find('laravel')) {
            $this->io->writeln('<info>Using Laravel installer</info>');
            $this->runner->run(['laravel', 'new', $projectName, '--pest', '--no-interaction'], $parentDir);
        } else {
            $this->io->writeln('<info>Laravel installer not found, using composer create-project</info>');
            $this->runner->run(
                ['composer', 'create-project', 'laravel/laravel', $projectName, '--prefer-dist', '--no-progress', '--no-interaction'],
                $parentDir,
            );
        }

        $this->io->writeln('<info>Requiring api-platform/laravel</info>');
        $this->runner->run(['composer', 'require', 'api-platform/laravel'], $projectDir);

        $this->io->writeln('<info>Running api-platform:install</info>');
        $this->runner->run(['php', 'artisan', 'api-platform:install'], $projectDir);

        $this->io->writeln('<info>Configuring API Platform</info>');
        $configPath = $projectDir.'/config/api-platform.php';
        $patched = $this->patcher->patch((string) file_get_contents($configPath), $opts->formats, $opts->docs);
        FileWriter::write($configPath, $patched);

        $this->patchAppUrl($projectDir);
        $this->setupWelcomePage($projectDir);

        if ($opts->withAdmin) {
            (new LaravelAdminScaffold($this->io))->run($projectDir);
        }

        $this->io->success(sprintf('Project created successfully at %s', $projectDir));
        $this->io->writeln('');
        $this->io->writeln('<comment>Next steps:</comment>');
        $this->io->writeln(sprintf('  cd %s', $projectName));
        $this->io->writeln('  npm run dev          # in one terminal');
        $this->io->writeln('  php artisan serve    # in another');
        $this->io->writeln('  open http://localhost:8000');
        if ($opts->withAdmin) {
            $this->io->writeln('  open http://localhost:8000/admin');
        }
        $this->io->writeln('');

        return Command::SUCCESS;
    }

    /**
     * Sets APP_URL to match the `php artisan serve` default port so the Vite
     * plugin advertises the URL the user will actually load in the browser.
     * Idempotent: only rewrites a bare `http://localhost` (Laravel default).
     */
    public static function patchAppUrlEnv(string $envContent, string $appUrl): string
    {
        // `\r?` keeps the patch working on CRLF-encoded .env files (Windows
        // editors, .env shipped via Composer on a non-Unix host).
        return preg_replace(
            '/^APP_URL=http:\/\/localhost\r?$/m',
            'APP_URL='.$appUrl,
            $envContent,
        ) ?? $envContent;
    }

    /**
     * Appends an ES module import to a Laravel `resources/js/app.js`. Idempotent.
     */
    public static function appendAppJsImport(string $existing, string $modulePath): string
    {
        $line = sprintf("import '%s';", $modulePath);
        if (str_contains($existing, $line)) {
            return $existing;
        }

        $sep = str_ends_with($existing, "\n") || '' === $existing ? '' : "\n";

        return $existing.$sep.$line."\n";
    }

    private function patchAppUrl(string $projectDir): void
    {
        $envFile = $projectDir.'/.env';
        if (!is_file($envFile)) {
            return;
        }
        $content = (string) file_get_contents($envFile);
        $patched = self::patchAppUrlEnv($content, 'http://localhost:8000');
        if ($patched !== $content) {
            FileWriter::write($envFile, $patched);
        }
    }

    private function setupWelcomePage(string $projectDir): void
    {
        $this->io->writeln('<info>Installing front-end welcome page</info>');

        $this->fs->copy(
            Templates::path('laravel-welcome.blade.php'),
            $projectDir.'/resources/views/welcome.blade.php',
            true,
        );
        $this->fs->copy(
            Templates::path('laravel-resources.js'),
            $projectDir.'/resources/js/api-platform-resources.js',
            true,
        );

        $appJs = $projectDir.'/resources/js/app.js';
        if (!is_file($appJs)) {
            throw new RuntimeException(sprintf('Could not find %s.', $appJs));
        }
        FileWriter::write(
            $appJs,
            self::appendAppJsImport((string) file_get_contents($appJs), './api-platform-resources'),
        );

        $this->io->writeln('<info>Installing JavaScript dependencies</info>');
        // `npm install <pkg>...` installs declared deps from package.json and
        // adds the new packages in a single dependency walk.
        $this->runner->run(['npm', 'install', ...self::JS_PACKAGES], $projectDir);
    }
}
