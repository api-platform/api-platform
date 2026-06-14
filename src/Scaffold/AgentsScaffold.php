<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Writes agent-instruction files at the project root so AI coding agents read
 * the current API Platform documentation instead of stale training data.
 *
 * The canonical AGENTS.md is maintained in api-platform/skillset and fetched
 * from its main branch — a single source of truth, no bundled copy to drift.
 * CLAUDE.md is a thin @AGENTS.md import so Claude Code shares the same baseline
 * before the skillset plugin layers richer skills on top.
 */
final class AgentsScaffold
{
    public const SKILLSET_AGENTS_URL = 'https://raw.githubusercontent.com/api-platform/skillset/main/AGENTS.md';

    /** @var callable(): ?string */
    private $fetch;

    /**
     * @param (callable(): ?string)|null $fetch fetches the canonical AGENTS.md; defaults to a download from skillset
     */
    public function __construct(
        private readonly SymfonyStyle $io,
        ?callable $fetch = null,
    ) {
        $this->fetch = $fetch ?? self::download(...);
    }

    public function write(string $projectDir): void
    {
        $agents = ($this->fetch)();
        if (null === $agents || '' === $agents) {
            // Non-critical: never abort a scaffold over a doc file the project
            // can add later. The skillset URL may be unreachable (offline, rate
            // limit), so warn and move on.
            $this->io->warning('Could not fetch AGENTS.md from the API Platform skillset; skipping AI agent instruction files.');

            return;
        }

        $this->io->writeln('<info>Writing AI agent instructions (AGENTS.md, CLAUDE.md)</info>');
        FileWriter::write($projectDir.'/AGENTS.md', $agents);
        FileWriter::write($projectDir.'/CLAUDE.md', "@AGENTS.md\n");
    }

    private static function download(): ?string
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'header' => 'User-Agent: api-platform-installer',
            ],
        ]);
        $content = @file_get_contents(self::SKILLSET_AGENTS_URL, false, $context);

        return false === $content ? null : $content;
    }
}
