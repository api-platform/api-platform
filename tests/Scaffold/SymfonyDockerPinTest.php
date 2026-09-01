<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Tests\Scaffold;

use ApiPlatform\Installer\Scaffold\SymfonyDockerPin;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class SymfonyDockerPinTest extends TestCase
{
    private const OLD = '3c0d1772e807a2e54b6c9c53471ef25c5782e275';
    private const NEW = '422756611d61e0108600ed7ec1370ec677d0e8d0';

    private static function source(string $sha = self::OLD): string
    {
        return <<<PHP
            final class SymfonyScaffold
            {
                private const SYMFONY_DOCKER_REPO = 'https://github.com/dunglas/symfony-docker';
                public const SYMFONY_DOCKER_REF = '{$sha}';
            }
            PHP;
    }

    public function testReadsThePinnedSha(): void
    {
        $this->assertSame(self::OLD, SymfonyDockerPin::read(self::source()));
    }

    public function testReadFailsWhenTheConstantMoved(): void
    {
        $this->expectException(RuntimeException::class);

        SymfonyDockerPin::read('<?php final class SymfonyScaffold {}');
    }

    public function testReplaceSwapsOnlyTheRefConstant(): void
    {
        $patched = SymfonyDockerPin::replace(self::source(), self::NEW);

        $this->assertSame(self::NEW, SymfonyDockerPin::read($patched));
        $this->assertStringContainsString("SYMFONY_DOCKER_REPO = 'https://github.com/dunglas/symfony-docker'", $patched);
    }

    public function testReplaceRejectsAShaThatIsNotAFullSha1(): void
    {
        $this->expectException(InvalidArgumentException::class);

        SymfonyDockerPin::replace(self::source(), 'main');
    }

    public function testReplaceFailsWhenItWouldBeANoOp(): void
    {
        // A silent no-op would let the bump workflow open an empty PR.
        $this->expectException(RuntimeException::class);

        SymfonyDockerPin::replace(self::source(), self::OLD);
    }

    public function testKeepsOnlyUpstreamPathsTheScaffoldCopies(): void
    {
        $changed = [
            'Dockerfile',
            'frankenphp/Caddyfile',
            '.devcontainer/devcontainer.json',
            'docs/agents.md',
            '.github/workflows/ci.yaml',
            'README.md',
        ];

        $this->assertSame(
            ['Dockerfile', 'frankenphp/Caddyfile', '.devcontainer/devcontainer.json'],
            SymfonyDockerPin::copiedPaths($changed),
        );
    }

    public function testDirectoryMatchingStopsAtAPathBoundary(): void
    {
        $this->assertSame([], SymfonyDockerPin::copiedPaths(['frankenphp-extra/Caddyfile', 'Dockerfile.bak']));
    }
}
