<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

/**
 * Runs subprocesses with stdout/stderr streamed to the console.
 */
final class ProcessRunner
{
    public function __construct(
        private readonly SymfonyStyle $io,
    ) {
    }

    /**
     * @param array<string>             $command
     * @param array<string, string>|null $env
     */
    public function run(array $command, ?string $cwd = null, ?array $env = null, ?float $timeout = 600): void
    {
        $this->io->writeln(sprintf('<comment>$ %s</>', implode(' ', $command)));

        $process = new Process($command, $cwd, $env, null, $timeout);
        $process->setTty(Process::isTtySupported());
        $process->run(function (string $type, string $buffer): void {
            $this->io->write($buffer);
        });

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf('Command failed: %s', implode(' ', $command)));
        }
    }
}
