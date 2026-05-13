<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

use ApiPlatform\Installer\Templates;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

final class PwaScaffold
{
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

    public function run(string $projectDir, string $apiDir): void
    {
        // The Flex recipe creates config/packages/nelmio_cors.yaml pre-configured
        // to read CORS_ALLOW_ORIGIN from the environment.
        $this->io->writeln('<info>Requiring nelmio/cors-bundle</info>');
        $this->runner->run(['composer', 'require', 'nelmio/cors-bundle'], $apiDir);

        $envFile = $apiDir.'/.env';
        if (!is_file($envFile)) {
            throw new \RuntimeException(sprintf('Could not find %s.', $envFile));
        }
        file_put_contents(
            $envFile,
            "\nCORS_ALLOW_ORIGIN=^https?://localhost(:[0-9]+)?$\n",
            \FILE_APPEND,
        );

        $pwaDir = $projectDir.'/pwa';
        $this->io->writeln('<info>Creating Next.js app with create-next-app</info>');
        $this->runner->run(['npx', 'create-next-app', 'pwa', '--use-pnpm'], $projectDir);

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
    }
}
