<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

use ApiPlatform\Installer\Templates;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Scaffolds a sibling `admin/` directory containing a Vite + React SPA
 * wired to `@api-platform/admin`. The SPA is fully standalone: it ships
 * its own package.json and runs against the API entrypoint configured
 * via the `VITE_ENTRYPOINT` env var (defaulting to `window.location.origin`).
 */
final class SymfonyAdminScaffold
{
    private const TEMPLATE_FILES = [
        // template name => destination relative to admin/
        'admin-index.html' => 'index.html',
        'admin-package.json' => 'package.json',
        'admin-vite.config.ts' => 'vite.config.ts',
        'admin-tsconfig.json' => 'tsconfig.json',
        'admin-app.tsx' => 'src/App.tsx',
        'admin-main.tsx' => 'src/main.tsx',
    ];

    private readonly Filesystem $fs;
    private readonly ProcessRunner $runner;

    public function __construct(
        private readonly SymfonyStyle $io,
    ) {
        $this->fs = new Filesystem();
        $this->runner = new ProcessRunner($io);
    }

    public function run(string $projectDir, string $apiEntrypoint): void
    {
        $adminDir = $projectDir.'/admin';
        $this->io->writeln('<info>Creating React-admin SPA</info>');
        $this->fs->mkdir($adminDir.'/src');

        foreach (self::TEMPLATE_FILES as $template => $relativePath) {
            $this->fs->copy(Templates::path($template), $adminDir.'/'.$relativePath, true);
        }

        $envFile = $adminDir.'/.env';
        $existing = is_file($envFile) ? (string) file_get_contents($envFile) : '';
        FileWriter::write($envFile, self::appendEntrypointEnv($existing, $apiEntrypoint));

        $this->printEntrypointHint($apiEntrypoint);

        $this->io->writeln('<info>Installing admin JavaScript dependencies</info>');
        $this->runner->run(['npm', 'install'], $adminDir);
    }

    private function printEntrypointHint(string $apiEntrypoint): void
    {
        // appendEntrypointEnv is idempotent and refuses to overwrite, so a
        // user whose actual server runs on another host/port needs a clear
        // pointer at the file to edit instead of guessing.
        $this->io->writeln(sprintf(
            '<comment>Admin API entrypoint set to %s. Edit admin/.env (VITE_ENTRYPOINT) if your API runs elsewhere.</comment>',
            $apiEntrypoint,
        ));
    }

    /**
     * Adds `VITE_ENTRYPOINT=...` to the admin app's `.env`. Idempotent:
     * a custom value is left untouched on re-run.
     */
    public static function appendEntrypointEnv(string $existing, string $entrypoint): string
    {
        if (preg_match('/^VITE_ENTRYPOINT=/m', $existing)) {
            return $existing;
        }

        $sep = '' === $existing || str_ends_with($existing, "\n") ? '' : "\n";

        return $existing.$sep.'VITE_ENTRYPOINT='.$entrypoint."\n";
    }
}
