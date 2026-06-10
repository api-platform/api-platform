<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Tests\Scaffold;

use ApiPlatform\Installer\Scaffold\LaravelConfigPatcher;
use PHPUnit\Framework\TestCase;

final class LaravelConfigPatcherTest extends TestCase
{
    private const FIXTURE = <<<'PHP'
<?php

declare(strict_types=1);

return [
    // Title shown in the API docs.
    'title' => 'API Platform',

    // Formats supported by the API.
    'formats' => [
        'jsonld' => ['application/ld+json'],
    ],

    'docs_formats' => [
        'jsonld' => ['application/ld+json'],
    ],

    'swagger_ui' => [
        // Enables Swagger UI at /api.
        'enabled' => true,
    ],

    'redoc' => [
        'enabled' => true,
    ],

    'scalar' => [
        'enabled' => true,
        'extra_configuration' => [],
    ],
];
PHP;

    public function testReplacesFormatsAndKeepsSwaggerUi(): void
    {
        $patched = (new LaravelConfigPatcher())->patch(
            self::FIXTURE,
            formats: ['jsonld', 'jsonapi', 'hal'],
            docs: ['swagger_ui'],
        );

        $this->assertStringContainsString("'jsonld' => ['application/ld+json']", $patched);
        $this->assertStringContainsString("'jsonapi' => ['application/vnd.api+json']", $patched);
        $this->assertStringContainsString("'jsonhal' => ['application/hal+json']", $patched);
        $this->assertStringContainsString("'enabled' => true", $patched);
        $this->assertStringContainsString('// Title shown in the API docs.', $patched, 'comments must be preserved');
        $this->assertStringContainsString('// Enables Swagger UI at /api.', $patched, 'inline comments must be preserved');
    }

    public function testDisablesSwaggerUiWhenNotInDocs(): void
    {
        $patched = (new LaravelConfigPatcher())->patch(
            self::FIXTURE,
            formats: ['jsonld'],
            docs: [],
        );

        $this->assertStringContainsString("'enabled' => false", $patched);
    }

    public function testKeepsScalarEnabledWhenInDocs(): void
    {
        $patched = (new LaravelConfigPatcher())->patch(
            self::FIXTURE,
            formats: ['jsonld'],
            docs: ['scalar'],
        );

        // Both swagger_ui and scalar blocks contain 'enabled' — scalar must stay true.
        $this->assertMatchesRegularExpression(
            "/'scalar' => \[\s*'enabled' => true/",
            $patched,
        );
    }

    public function testDisablesScalarWhenNotInDocs(): void
    {
        $patched = (new LaravelConfigPatcher())->patch(
            self::FIXTURE,
            formats: ['jsonld'],
            docs: ['swagger_ui'],
        );

        $this->assertMatchesRegularExpression(
            "/'scalar' => \[\s*'enabled' => false/",
            $patched,
        );
    }

    public function testKeepsRedocEnabledWhenInDocs(): void
    {
        $patched = (new LaravelConfigPatcher())->patch(
            self::FIXTURE,
            formats: ['jsonld'],
            docs: ['redoc'],
        );

        $this->assertMatchesRegularExpression(
            "/'redoc' => \[\s*'enabled' => true/",
            $patched,
        );
    }

    public function testDisablesRedocWhenNotInDocs(): void
    {
        $patched = (new LaravelConfigPatcher())->patch(
            self::FIXTURE,
            formats: ['jsonld'],
            docs: ['swagger_ui'],
        );

        $this->assertMatchesRegularExpression(
            "/'redoc' => \[\s*'enabled' => false/",
            $patched,
        );
    }

    public function testKeepsSurroundingArrayKeysIntact(): void
    {
        $patched = (new LaravelConfigPatcher())->patch(
            self::FIXTURE,
            formats: ['jsonld'],
            docs: ['swagger_ui'],
        );

        // 'title' and 'docs_formats' must still be there, not replaced.
        $this->assertStringContainsString("'title' => 'API Platform'", $patched);
        $this->assertStringContainsString("'docs_formats' => [", $patched);
    }

    public function testRejectsConfigWithoutTopLevelReturn(): void
    {
        $this->expectException(\RuntimeException::class);
        (new LaravelConfigPatcher())->patch('<?php $foo = 1;', ['jsonld'], ['swagger_ui']);
    }

    public function testTargetsNewestSupportedPhpVersion(): void
    {
        // A hardcoded version blocks every future PHP syntax the host already
        // supports. The patcher should follow the parser's newest-supported
        // version so api-platform/laravel can ship configs with newer syntax.
        $patcher = new LaravelConfigPatcher();
        $reflection = new \ReflectionMethod($patcher, 'parserPhpVersion');
        $version = $reflection->invoke($patcher);

        $this->assertEquals(\PhpParser\PhpVersion::getNewestSupported(), $version);
    }
}
