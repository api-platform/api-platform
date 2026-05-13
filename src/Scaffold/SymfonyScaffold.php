<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

use ApiPlatform\Installer\Templates;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Yaml\Yaml;

final class SymfonyScaffold
{
    private const SYMFONY_DOCKER_REPO = 'https://github.com/dunglas/symfony-docker';
    private const DOCKER_FILES = [
        'Dockerfile',
        '.dockerignore',
        'compose.yaml',
        'compose.override.yaml',
        'compose.prod.yaml',
        'frankenphp',
    ];
    private const KNOWN_FORMATS = [
        'jsonld' => 'application/ld+json',
        'jsonapi' => 'application/vnd.api+json',
        'hal' => 'application/hal+json',
    ];

    private readonly Filesystem $fs;
    private readonly ProcessRunner $runner;

    public function __construct(
        private readonly SymfonyStyle $io,
    ) {
        $this->fs = new Filesystem();
        $this->runner = new ProcessRunner($io);
    }

    public function run(string $projectDir, string $projectName, ScaffoldOptions $opts): int
    {
        $this->checkRequiredBinaries($opts);

        $parentDir = \dirname($projectDir);

        if ($opts->withPwa) {
            $apiDir = $projectDir.'/api';
            $this->fs->mkdir($projectDir);
            $this->io->writeln('<info>Creating Symfony skeleton</info>');
            $this->runner->run(
                ['composer', 'create-project', 'symfony/skeleton', 'api', '--prefer-dist', '--no-progress', '--no-interaction'],
                $projectDir,
            );
        } else {
            $apiDir = $projectDir;
            $this->io->writeln('<info>Creating Symfony skeleton</info>');
            $this->runner->run(
                ['composer', 'create-project', 'symfony/skeleton', $projectName, '--prefer-dist', '--no-progress', '--no-interaction'],
                $parentDir,
            );
        }

        if ($opts->withDocker) {
            $this->setupDocker($apiDir);
        }

        $this->io->writeln('<info>Requiring API Platform packages</info>');
        $packages = ['api-platform/symfony', 'api-platform/doctrine-orm', 'symfony/twig-bundle', 'phpstan/phpdoc-parser'];
        foreach ($opts->formats as $f) {
            $packages[] = match ($f) {
                'jsonapi' => 'api-platform/json-api',
                'hal' => 'api-platform/hal',
                default => null,
            };
        }
        $packages = array_values(array_filter($packages));
        $this->runner->run(['composer', 'require', ...$packages], $apiDir);

        $this->io->writeln('<info>Writing API Platform configuration</info>');
        $this->writeApiPlatformConfig($apiDir, $opts);

        $routesDir = $apiDir.'/config/routes';
        $this->fs->mkdir($routesDir);
        $this->fs->copy(Templates::path('routes.yaml'), $routesDir.'/api_platform.yaml', true);

        $resourceDir = $apiDir.'/src/ApiResource';
        $this->fs->mkdir($resourceDir);
        $this->fs->copy(Templates::path('Greetings.php'), $resourceDir.'/Greetings.php', true);

        if ($opts->withPwa) {
            $this->io->writeln('<info>Setting up Next.js PWA</info>');
            (new PwaScaffold($this->io))->run($projectDir, $apiDir);
        }

        $this->io->success(sprintf('Project created successfully at %s', $projectDir));
        $this->printNextSteps($projectName, $opts);

        return Command::SUCCESS;
    }

    private function checkRequiredBinaries(ScaffoldOptions $opts): void
    {
        $required = ['composer'];
        if ($opts->withDocker) {
            $required[] = 'git';
            $required[] = 'docker';
        }
        if ($opts->withPwa) {
            array_push($required, 'node', 'npx', 'pnpm');
        }

        $finder = new ExecutableFinder();
        $missing = array_values(array_filter($required, static fn (string $b): bool => null === $finder->find($b)));
        if ([] !== $missing) {
            throw new \RuntimeException(sprintf('Missing required binaries in PATH: %s.', implode(', ', $missing)));
        }
    }

    private function setupDocker(string $apiDir): void
    {
        $this->io->writeln('<info>Fetching Docker infrastructure from symfony-docker</info>');
        $tmpDir = sys_get_temp_dir().'/symfony-docker-'.bin2hex(random_bytes(4));
        $this->fs->mkdir($tmpDir);

        try {
            $this->runner->run(['git', 'clone', '--depth=1', self::SYMFONY_DOCKER_REPO, $tmpDir]);
            foreach (self::DOCKER_FILES as $name) {
                $src = $tmpDir.'/'.$name;
                $dst = $apiDir.'/'.$name;
                if (is_dir($src)) {
                    $this->fs->mirror($src, $dst);
                } else {
                    $this->fs->copy($src, $dst, true);
                }
            }
        } finally {
            $this->fs->remove($tmpDir);
        }

        $this->io->writeln('<info>Writing API Platform Compose override</info>');
        (new ComposeOverrideWriter())->write($apiDir);
    }

    private function writeApiPlatformConfig(string $apiDir, ScaffoldOptions $opts): void
    {
        $configDir = $apiDir.'/config/packages';
        $this->fs->mkdir($configDir);

        $formats = [];
        foreach ($opts->formats as $f) {
            if (isset(self::KNOWN_FORMATS[$f])) {
                $formats[$f] = [self::KNOWN_FORMATS[$f]];
            }
        }

        $config = [
            'title' => 'Hello API Platform',
            'version' => '1.0.0',
            'formats' => $formats,
            'enable_swagger_ui' => \in_array('swagger_ui', $opts->docs, true),
            'enable_re_doc' => \in_array('redoc', $opts->docs, true),
            'defaults' => [
                'stateless' => true,
                'cache_headers' => [
                    'vary' => ['Content-Type', 'Authorization', 'Origin'],
                ],
            ],
        ];

        if ([] !== $opts->docs) {
            $config['formats']['html'] = ['text/html'];
        } else {
            $config['enable_docs'] = false;
        }

        file_put_contents($configDir.'/api_platform.yaml', Yaml::dump(['api_platform' => $config], 4, 4));
    }

    private function printNextSteps(string $projectName, ScaffoldOptions $opts): void
    {
        $this->io->writeln('');
        $this->io->writeln('<comment>Next steps:</comment>');
        $cdTarget = $opts->withPwa ? "$projectName/api" : $projectName;
        $this->io->writeln(sprintf('  cd %s', $cdTarget));

        if ($opts->withDocker) {
            $this->io->writeln('  docker compose up --wait');
            $this->io->writeln('  open https://localhost');
        } else {
            $this->io->writeln(null !== (new ExecutableFinder())->find('symfony') ? '  symfony server:start' : '  php -S 0.0.0.0:8000 -t public');
            $this->io->writeln('  open http://localhost:8000');
        }

        if ($opts->withPwa) {
            $this->io->writeln('');
            $this->io->writeln('  cd ../pwa');
            $this->io->writeln('  pnpm dev');
            $this->io->writeln('  open http://localhost:3000');
            $this->io->writeln('');
            $this->io->writeln('CORS is pre-configured to allow requests from localhost.');
        }
        $this->io->writeln('');
    }
}
