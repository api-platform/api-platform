<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Tests;

use ApiPlatform\Installer\InstallerCommand;
use ApiPlatform\Installer\Scaffold\ScaffoldOptions;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Tester\CommandTester;

final class InstallerCommandTest extends TestCase
{
    /**
     * @return array<array{string}>
     */
    public static function validNames(): array
    {
        return [['my-app'], ['MyApp'], ['app1'], ['app.test'], ['app_test'], ['1app']];
    }

    /**
     * @return array<array{string}>
     */
    public static function invalidNames(): array
    {
        return [[''], ['-leading-dash'], ['has space'], ['has/slash'], ['has@at']];
    }

    #[DataProvider('validNames')]
    public function testAcceptsValidNames(string $name): void
    {
        $this->assertSame($name, InstallerCommand::validateName($name));
    }

    #[DataProvider('invalidNames')]
    public function testRejectsInvalidNames(string $name): void
    {
        $this->expectException(InvalidArgumentException::class);
        InstallerCommand::validateName($name);
    }

    public function testRejectsExistingDirectory(): void
    {
        $tmp = sys_get_temp_dir().'/installer-test-'.bin2hex(random_bytes(4));
        mkdir($tmp);
        try {
            $cwd = getcwd();
            chdir(\dirname($tmp));
            $tester = $this->tester();
            $tester->execute(
                ['name' => basename($tmp), '--framework' => 'symfony', '--with-docker' => false, '--with-pwa' => false],
                ['interactive' => false],
            );
            chdir($cwd);
            $this->assertSame(2, $tester->getStatusCode());
            $this->assertStringContainsString('already exists', $tester->getDisplay());
        } finally {
            rmdir($tmp);
        }
    }

    public function testRejectsInvalidProjectName(): void
    {
        $tester = $this->tester();
        $tester->execute(['name' => 'has space', '--framework' => 'symfony'], ['interactive' => false]);
        $this->assertSame(2, $tester->getStatusCode());
        $this->assertStringContainsString('start with a letter', $tester->getDisplay());
    }

    public function testRejectsUnknownFramework(): void
    {
        $tester = $this->tester();
        $tester->execute(['name' => 'demo', '--framework' => 'rails'], ['interactive' => false]);
        $this->assertSame(2, $tester->getStatusCode());
        $this->assertStringContainsString('Unsupported framework', $tester->getDisplay());
    }

    public function testRejectsUnknownDocs(): void
    {
        $tester = $this->tester();
        $tester->execute(['name' => 'demo', '--framework' => 'symfony', '--docs' => ['foo']], ['interactive' => false]);
        $this->assertSame(2, $tester->getStatusCode());
        $this->assertStringContainsString('Unknown --docs', $tester->getDisplay());
    }

    public function testRejectsWithDockerOnLaravel(): void
    {
        $tester = $this->tester();
        $tester->execute(
            ['name' => 'demo', '--framework' => 'laravel', '--with-docker' => true],
            ['interactive' => false],
        );
        $this->assertSame(2, $tester->getStatusCode());
        $this->assertStringContainsString('--with-docker is not supported with Laravel', $tester->getDisplay());
    }

    public function testRejectsWithPwaOnLaravel(): void
    {
        $tester = $this->tester();
        $tester->execute(
            ['name' => 'demo', '--framework' => 'laravel', '--with-pwa' => true],
            ['interactive' => false],
        );
        $this->assertSame(2, $tester->getStatusCode());
        $this->assertStringContainsString('--with-pwa is not supported with Laravel', $tester->getDisplay());
    }

    public function testDocsOptionDescriptionMentionsScalar(): void
    {
        $command = new InstallerCommand();
        $description = $command->getDefinition()->getOption('docs')->getDescription();
        $this->assertStringContainsString('scalar', $description);
    }

    public function testAcceptsScalarAsDocsOption(): void
    {
        $this->assertContains('scalar', InstallerCommand::DOCS);
    }

    public function testReportsDevVersionWhenPlaceholderUnsubstituted(): void
    {
        // With box's git-version replacement disabled (running from source),
        // the @package_version@ placeholder must collapse to a "dev" string.
        $this->assertSame('dev', InstallerCommand::version());
    }

    public function testNonInteractiveDefaultsSelectEveryFormatAndDocViewer(): void
    {
        $opts = $this->resolveDefaultOptions();

        $this->assertSame(InstallerCommand::FORMATS, $opts->formats);
        $this->assertSame(InstallerCommand::DOCS, $opts->docs);
    }

    public function testInteractiveMultiselectPromptsEmitNoPhpWarnings(): void
    {
        $tmp = sys_get_temp_dir().'/installer-test-'.bin2hex(random_bytes(4));
        mkdir($tmp);

        $errors = [];
        set_error_handler(static function (int $severity, string $message, string $file, int $line) use (&$errors): bool {
            $errors[] = ['severity' => $severity, 'message' => $message, 'file' => $file, 'line' => $line];

            return true;
        });

        try {
            $cwd = getcwd();
            chdir(\dirname($tmp));
            $tester = $this->tester();
            // Accept defaults for the two multiselect prompts (formats, docs).
            $tester->setInputs(['', '']);
            $tester->execute(
                ['name' => basename($tmp), '--framework' => 'symfony', '--with-docker' => false, '--with-pwa' => false],
                ['interactive' => true],
            );
            if (false !== $cwd) {
                chdir($cwd);
            }
        } finally {
            restore_error_handler();
            rmdir($tmp);
        }

        $warnings = array_values(array_filter($errors, static fn (array $e): bool => \E_WARNING === $e['severity']));
        $this->assertSame([], $warnings, 'Expected no PHP warnings from interactive prompts, got: '.json_encode($warnings));
    }

    private function tester(): CommandTester
    {
        $command = new InstallerCommand();
        $app = new Application();
        $app->add($command);

        return new CommandTester($app->find((string) $command->getName()));
    }

    private function resolveDefaultOptions(): ScaffoldOptions
    {
        $command = new InstallerCommand();
        $input = new ArrayInput([
            'name' => 'demo',
            '--framework' => 'symfony',
            '--with-docker' => false,
            '--with-pwa' => false,
        ]);
        $input->bind($command->getDefinition());
        $input->setInteractive(false);
        $io = new SymfonyStyle($input, new BufferedOutput());

        $method = new \ReflectionMethod($command, 'resolveOptions');

        return $method->invoke($command, $io, $input, InstallerCommand::FRAMEWORK_SYMFONY);
    }
}
