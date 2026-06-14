<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Tests\Scaffold;

use ApiPlatform\Installer\Scaffold\AgentsScaffold;
use ApiPlatform\Installer\Scaffold\ScaffoldOptions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class AgentsScaffoldTest extends TestCase
{
    public function testWritesFetchedAgentsAndThinClaudeImport(): void
    {
        $dir = sys_get_temp_dir().'/agents-scaffold-test-'.bin2hex(random_bytes(4));
        mkdir($dir);

        try {
            $scaffold = new AgentsScaffold($this->io(), static fn (): string => "# API Platform stub\n");
            $scaffold->write($dir);

            $this->assertSame("# API Platform stub\n", (string) file_get_contents($dir.'/AGENTS.md'));
            // CLAUDE.md stays a thin @-import so there is a single source of project guidance.
            $this->assertSame("@AGENTS.md\n", (string) file_get_contents($dir.'/CLAUDE.md'));
        } finally {
            @unlink($dir.'/AGENTS.md');
            @unlink($dir.'/CLAUDE.md');
            @rmdir($dir);
        }
    }

    public function testSkipsBothFilesWhenFetchFails(): void
    {
        $dir = sys_get_temp_dir().'/agents-scaffold-test-'.bin2hex(random_bytes(4));
        mkdir($dir);

        try {
            // A null fetch (offline, rate limit) must not abort the scaffold.
            $scaffold = new AgentsScaffold($this->io(), static fn (): ?string => null);
            $scaffold->write($dir);

            $this->assertFileDoesNotExist($dir.'/AGENTS.md');
            $this->assertFileDoesNotExist($dir.'/CLAUDE.md');
        } finally {
            @rmdir($dir);
        }
    }

    public function testWithAgentsDefaultsToTrue(): void
    {
        $opts = new ScaffoldOptions(withPwa: false, withDocker: false, formats: [], docs: []);

        $this->assertTrue($opts->withAgents);
    }

    private function io(): SymfonyStyle
    {
        return new SymfonyStyle(new ArrayInput([]), new BufferedOutput());
    }
}
