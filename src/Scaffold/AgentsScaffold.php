<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

use ApiPlatform\Installer\Templates;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Writes agent-instruction files at the project root so AI coding agents read
 * the current API Platform documentation instead of stale training data.
 *
 * AGENTS.md is the cross-agent convention (Cursor, GitHub Copilot, OpenAI Codex,
 * Gemini); CLAUDE.md is a thin @AGENTS.md import so Claude Code shares the same
 * baseline before the skillset plugin layers richer skills on top.
 */
final class AgentsScaffold
{
    private readonly Filesystem $fs;

    public function __construct(
        private readonly SymfonyStyle $io,
    ) {
        $this->fs = new Filesystem();
    }

    public function write(string $projectDir): void
    {
        $this->io->writeln('<info>Writing AI agent instructions (AGENTS.md, CLAUDE.md)</info>');
        $this->fs->copy(Templates::path('AGENTS.md'), $projectDir.'/AGENTS.md', true);
        $this->fs->copy(Templates::path('CLAUDE.md'), $projectDir.'/CLAUDE.md', true);
    }
}
