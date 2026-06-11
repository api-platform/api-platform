<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

use ApiPlatform\Installer\Templates;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

final class NextPwaScaffold
{
    private const PNPM_BUILD_APPROVALS = ['sharp', 'unrs-resolver'];
    private const PNPM_BUILD_APPROVAL_PLACEHOLDER = 'set this to true or false';
    private const CORS_ALLOW_ORIGIN = "'^https?://(localhost|127\\.0\\.0\\.1)(:[0-9]+)?$'";

    private const JS_PACKAGES = [
        '@api-platform/api-doc-parser',
        'github:api-platform/zod',
        '@api-platform/ld',
        '@api-platform/mercure',
    ];

    private readonly Filesystem $fs;
    private readonly ProcessRunner $runner;

    public function __construct(
        private readonly SymfonyStyle $io,
    ) {
        $this->fs = new Filesystem();
        $this->runner = new ProcessRunner($io);
    }

    public function run(string $projectDir, string $apiDir, string $apiEntrypoint = 'https://localhost'): void
    {
        // The Flex recipe creates config/packages/nelmio_cors.yaml pre-configured
        // to read CORS_ALLOW_ORIGIN from the environment.
        $this->io->writeln('<info>Requiring nelmio/cors-bundle</info>');
        $this->runner->run(['composer', 'require', 'nelmio/cors-bundle'], $apiDir);

        $envFile = $apiDir.'/.env';
        if (!is_file($envFile)) {
            throw new \RuntimeException(sprintf('Could not find %s.', $envFile));
        }
        file_put_contents($envFile, self::patchCorsAllowOriginEnv((string) file_get_contents($envFile)));

        $pwaDir = $projectDir.'/pwa';
        $this->io->writeln('<info>Creating Next.js app with create-next-app</info>');
        $this->runner->run(self::createNextAppCommand('pwa'), $projectDir);
        if ($this->approvePnpmBuilds($pwaDir)) {
            $this->io->writeln('<info>Approving required pnpm build scripts</info>');
            $this->runner->run(['pnpm', 'install'], $pwaDir);
        }

        $this->io->writeln('<info>Installing API Platform frontend libraries</info>');
        $this->runner->run(['pnpm', 'add', ...self::JS_PACKAGES], $pwaDir);

        $pagePath = null;
        foreach ([
            $pwaDir.'/app/page.tsx',
            $pwaDir.'/src/app/page.tsx',
            $pwaDir.'/pages/index.tsx',
            $pwaDir.'/src/pages/index.tsx',
        ] as $candidate) {
            if (is_file($candidate)) {
                $pagePath = $candidate;
                break;
            }
        }
        if (null === $pagePath) {
            throw new \RuntimeException(sprintf('Could not find Next.js entry page in %s.', $pwaDir));
        }

        $this->fs->copy(Templates::path('pwa-page.tsx'), $pagePath, true);

        $envLocalFile = $pwaDir.'/.env.local';
        $existingEnv = is_file($envLocalFile) ? (string) file_get_contents($envLocalFile) : '';
        file_put_contents($envLocalFile, self::appendNextPublicApiEntrypointEnv($existingEnv, $apiEntrypoint));
    }

    /**
     * Builds the `npx create-next-app` command line used to scaffold the PWA.
     * The flags pin TypeScript + App Router so the entry-page lookup
     * downstream finds `app/page.tsx`, and `--yes` skips every interactive
     * prompt so a TTY user can't pick incompatible options.
     *
     * @return list<string>
     */
    public static function createNextAppCommand(string $appName): array
    {
        return ['npx', '--yes', 'create-next-app', $appName, '--use-pnpm', '--yes', '--ts', '--app'];
    }

    /**
     * Adds `NEXT_PUBLIC_API_ENTRYPOINT=...` to the PWA `.env.local` so the
     * page targets the actual API URL chosen at install time. Idempotent: a
     * custom value is left untouched on re-run.
     */
    public static function appendNextPublicApiEntrypointEnv(string $existing, string $entrypoint): string
    {
        if (preg_match('/^NEXT_PUBLIC_API_ENTRYPOINT=/m', $existing)) {
            return $existing;
        }

        $sep = '' === $existing || str_ends_with($existing, "\n") ? '' : "\n";

        return $existing.$sep.'NEXT_PUBLIC_API_ENTRYPOINT='.$entrypoint."\n";
    }

    public static function approvePnpmWorkspaceBuilds(string $content): string
    {
        $config = Yaml::parse($content);
        if (!\is_array($config)) {
            return $content;
        }

        $allowBuilds = $config['allowBuilds'] ?? null;
        if (!\is_array($allowBuilds)) {
            return $content;
        }

        $changed = false;
        foreach (self::PNPM_BUILD_APPROVALS as $package) {
            if (($allowBuilds[$package] ?? null) !== self::PNPM_BUILD_APPROVAL_PLACEHOLDER) {
                continue;
            }

            $allowBuilds[$package] = true;
            $changed = true;
        }

        if (!$changed) {
            return $content;
        }

        $config['allowBuilds'] = $allowBuilds;

        $ignoredBuilds = $config['ignoredBuiltDependencies'] ?? null;
        if (\is_array($ignoredBuilds)) {
            $ignoredBuilds = array_values(array_diff($ignoredBuilds, self::PNPM_BUILD_APPROVALS));
            if ([] === $ignoredBuilds) {
                unset($config['ignoredBuiltDependencies']);
            } else {
                $config['ignoredBuiltDependencies'] = $ignoredBuilds;
            }
        }

        return Yaml::dump($config, 4, 2);
    }

    public static function patchCorsAllowOriginEnv(string $content): string
    {
        if (preg_match('/^CORS_ALLOW_ORIGIN=/m', $content)) {
            return $content;
        }

        $separator = str_ends_with($content, "\n") || '' === $content ? '' : "\n";

        return $content.$separator."\nCORS_ALLOW_ORIGIN=".self::CORS_ALLOW_ORIGIN."\n";
    }

    private function approvePnpmBuilds(string $pwaDir): bool
    {
        $workspaceFile = $pwaDir.'/pnpm-workspace.yaml';
        if (!is_file($workspaceFile)) {
            return false;
        }

        $content = (string) file_get_contents($workspaceFile);
        $patched = self::approvePnpmWorkspaceBuilds($content);
        if ($patched === $content) {
            return false;
        }

        file_put_contents($workspaceFile, $patched);

        return true;
    }
}
