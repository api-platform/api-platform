<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Tests\Scaffold;

use ApiPlatform\Installer\Scaffold\ProcessRunner;
use PHPUnit\Framework\TestCase;

final class ProcessRunnerTest extends TestCase
{
    public function testFormatsCommandWithSpacesAsShellQuoted(): void
    {
        // `implode(' ', $command)` produced a misleading `$ ...` line for
        // arguments containing spaces — a user copy-pasting it ran a
        // different command. Process::getCommandLine() applies the
        // platform-correct escaping so the printed line is runnable as-is.
        $rendered = ProcessRunner::formatCommand(['npm', 'install', 'a package with spaces']);

        $this->assertStringContainsString('npm', $rendered);
        $this->assertStringContainsString('install', $rendered);
        $this->assertMatchesRegularExpression('/[\'"]a package with spaces[\'"]/', $rendered);
    }

    public function testLeavesSimpleArgsAlone(): void
    {
        $rendered = ProcessRunner::formatCommand(['composer', 'require', 'symfony/console']);

        $this->assertStringContainsString('composer', $rendered);
        $this->assertStringContainsString('require', $rendered);
        $this->assertStringContainsString('symfony/console', $rendered);
    }
}
