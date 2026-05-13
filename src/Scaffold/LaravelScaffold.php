<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\ExecutableFinder;

final class LaravelScaffold
{
    private readonly ProcessRunner $runner;
    private readonly LaravelConfigPatcher $patcher;

    public function __construct(
        private readonly SymfonyStyle $io,
    ) {
        $this->runner = new ProcessRunner($io);
        $this->patcher = new LaravelConfigPatcher();
    }

    public function run(string $projectDir, string $projectName, ScaffoldOptions $opts): int
    {
        $finder = new ExecutableFinder();
        $missing = array_values(array_filter(['composer', 'php'], static fn (string $b): bool => null === $finder->find($b)));
        if ([] !== $missing) {
            throw new \RuntimeException(sprintf('Missing required binaries in PATH: %s.', implode(', ', $missing)));
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
        file_put_contents($configPath, $patched);

        $this->io->success(sprintf('Project created successfully at %s', $projectDir));
        $this->io->writeln('');
        $this->io->writeln('<comment>Next steps:</comment>');
        $this->io->writeln(sprintf('  cd %s', $projectName));
        $this->io->writeln('  php artisan serve');
        $this->io->writeln('  open http://localhost:8000');
        $this->io->writeln('');

        return Command::SUCCESS;
    }
}
