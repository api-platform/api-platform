<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Tests;

use ApiPlatform\Installer\InstallerCommand;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
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

    public function testReportsDevVersionWhenPlaceholderUnsubstituted(): void
    {
        // With box's git-version replacement disabled (running from source),
        // the @package_version@ placeholder must collapse to a "dev" string.
        $this->assertSame('dev', InstallerCommand::version());
    }

    private function tester(): CommandTester
    {
        $command = new InstallerCommand();
        $app = new Application();
        $app->add($command);

        return new CommandTester($app->find((string) $command->getName()));
    }
}
