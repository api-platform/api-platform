<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

final readonly class ScaffoldOptions
{
    /**
     * @param array<string> $formats
     * @param array<string> $docs
     */
    public function __construct(
        public bool $withPwa,
        public bool $withDocker,
        public array $formats,
        public array $docs,
        public bool $withAdmin = false,
        public bool $withAgents = true,
        public string $symfonyDockerRef = SymfonyScaffold::SYMFONY_DOCKER_REF,
    ) {
    }
}
