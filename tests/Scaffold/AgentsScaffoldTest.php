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
    public function testWritesAgentsAndClaudeFilesAtProjectRoot(): void
    {
        $dir = sys_get_temp_dir().'/agents-scaffold-test-'.bin2hex(random_bytes(4));
        mkdir($dir);

        try {
            (new AgentsScaffold(new SymfonyStyle(new ArrayInput([]), new BufferedOutput())))->write($dir);

            $this->assertFileExists($dir.'/AGENTS.md');
            $this->assertFileExists($dir.'/CLAUDE.md');

            $agents = (string) file_get_contents($dir.'/AGENTS.md');
            $this->assertStringContainsString('https://api-platform.com/docs', $agents);
            $this->assertStringContainsString('/plugin marketplace add api-platform/skillset', $agents);

            // CLAUDE.md stays a thin @-import so there is a single source of project guidance.
            $this->assertSame("@AGENTS.md\n", (string) file_get_contents($dir.'/CLAUDE.md'));
        } finally {
            @unlink($dir.'/AGENTS.md');
            @unlink($dir.'/CLAUDE.md');
            @rmdir($dir);
        }
    }

    public function testWithAgentsDefaultsToTrue(): void
    {
        $opts = new ScaffoldOptions(withPwa: false, withDocker: false, formats: [], docs: []);

        $this->assertTrue($opts->withAgents);
    }
}
