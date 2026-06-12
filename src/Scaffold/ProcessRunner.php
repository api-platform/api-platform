<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

use RuntimeException;
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
        $process = new Process($command, $cwd, $env, null, $timeout);
        $this->io->writeln(sprintf('<comment>$ %s</>', $process->getCommandLine()));

        $process->setTty(Process::isTtySupported());
        $process->run(function (string $type, string $buffer): void {
            $this->io->write($buffer);
        });

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf('Command failed: %s', $process->getCommandLine()));
        }
    }

    /**
     * Renders a command array as a copy-pasteable shell line, applying the
     * platform's argument escaping (Process::getCommandLine).
     *
     * @param array<string> $command
     */
    public static function formatCommand(array $command): string
    {
        return (new Process($command))->getCommandLine();
    }
}
