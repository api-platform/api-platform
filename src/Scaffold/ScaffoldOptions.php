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
    ) {
    }
}
