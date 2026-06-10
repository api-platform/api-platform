<?php

declare(strict_types=1);

namespace ApiPlatform\Installer;

use ApiPlatform\Installer\Scaffold\LaravelScaffold;
use ApiPlatform\Installer\Scaffold\ScaffoldOptions;
use ApiPlatform\Installer\Scaffold\SymfonyScaffold;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\ExecutableFinder;

#[AsCommand(name: 'api-platform', description: 'Scaffold a new API Platform project')]
final class InstallerCommand extends Command
{
    public const VERSION = '@package_version@';

    public const FRAMEWORK_SYMFONY = 'symfony';
    public const FRAMEWORK_LARAVEL = 'laravel';

    public const FORMATS = ['jsonld', 'jsonapi', 'hal'];
    public const DOCS = ['swagger_ui', 'redoc', 'scalar'];

    private const NAME_PATTERN = '/^[a-zA-Z0-9][a-zA-Z0-9._-]*$/';

    public static function version(): string
    {
        return str_starts_with(self::VERSION, '@') ? 'dev' : self::VERSION;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL, 'Project name')
            ->addOption('framework', null, InputOption::VALUE_REQUIRED, 'Framework: symfony or laravel')
            ->addOption('with-pwa', null, InputOption::VALUE_NEGATABLE, 'Include Next.js PWA (Symfony only)')
            ->addOption('with-admin', null, InputOption::VALUE_NEGATABLE, 'Include React-admin SPA')
            ->addOption('with-docker', null, InputOption::VALUE_NEGATABLE, 'Use Docker (Symfony only)')
            ->addOption('format', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'API formats (jsonld|jsonapi|hal); repeat for several')
            ->addOption('docs', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Documentation (swagger_ui|redoc|scalar); repeat for several, empty disables');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('API Platform installer');

        try {
            $name = $this->resolveProjectName($io, $input);
            $framework = $this->resolveFramework($io, $input);
            $opts = $this->resolveOptions($io, $input, $framework);
        } catch (InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        }

        $projectDir = (getcwd() ?: '.').\DIRECTORY_SEPARATOR.$name;
        if (file_exists($projectDir)) {
            $io->error(sprintf('Directory %s already exists.', $projectDir));

            return Command::INVALID;
        }

        $io->section(sprintf('Creating %s project "%s"', $framework, $name));

        return self::FRAMEWORK_SYMFONY === $framework
            ? (new SymfonyScaffold($io))->run($projectDir, $name, $opts)
            : (new LaravelScaffold($io))->run($projectDir, $name, $opts);
    }

    public static function validateName(string $name): string
    {
        if (!preg_match(self::NAME_PATTERN, $name)) {
            throw new InvalidArgumentException('Project name must start with a letter or digit and contain only letters, digits, hyphens, dots, or underscores.');
        }

        return $name;
    }

    private function resolveProjectName(SymfonyStyle $io, InputInterface $input): string
    {
        $name = $input->getArgument('name');
        if (\is_string($name) && '' !== $name) {
            return self::validateName($name);
        }

        $question = new Question('Project name', 'my-app');
        $question->setValidator(static fn ($v): string => self::validateName((string) $v));
        $question->setMaxAttempts(3);

        return (string) $io->askQuestion($question);
    }

    private function resolveFramework(SymfonyStyle $io, InputInterface $input): string
    {
        $framework = $input->getOption('framework');
        if (\is_string($framework) && '' !== $framework) {
            if (!\in_array($framework, [self::FRAMEWORK_SYMFONY, self::FRAMEWORK_LARAVEL], true)) {
                throw new InvalidArgumentException(sprintf('Unsupported framework "%s" (must be symfony or laravel).', $framework));
            }

            return $framework;
        }

        $question = new ChoiceQuestion('Framework', [self::FRAMEWORK_SYMFONY, self::FRAMEWORK_LARAVEL], self::FRAMEWORK_SYMFONY);

        return (string) $io->askQuestion($question);
    }

    private function resolveOptions(SymfonyStyle $io, InputInterface $input, string $framework): ScaffoldOptions
    {
        $withDocker = false;
        $withPwa = false;

        if (self::FRAMEWORK_SYMFONY === $framework) {
            $dockerOption = $input->getOption('with-docker');
            $withDocker = null !== $dockerOption
                ? (bool) $dockerOption
                : (bool) $io->askQuestion(new ConfirmationQuestion('Use Docker?', true));

            $pwaOption = $input->getOption('with-pwa');
            if (null !== $pwaOption) {
                $withPwa = (bool) $pwaOption;
            } else {
                $missing = $this->missingBinaries(['node', 'npx', 'pnpm']);
                if ([] !== $missing) {
                    $io->note(sprintf('Skipping PWA prompt: %s not found in PATH.', implode(', ', $missing)));
                } else {
                    $withPwa = (bool) $io->askQuestion(new ConfirmationQuestion('Include Next.js PWA?', false));
                }
            }
        } else {
            if (true === $input->getOption('with-docker')) {
                throw new InvalidArgumentException('--with-docker is not supported with Laravel.');
            }
            if (true === $input->getOption('with-pwa')) {
                throw new InvalidArgumentException('--with-pwa is not supported with Laravel.');
            }
        }

        $withAdmin = $this->resolveWithAdmin($io, $input);

        $formats = $this->resolveMulti($io, $input, 'format', self::FORMATS, self::FORMATS, 'API formats');
        $docs = $this->resolveMulti($io, $input, 'docs', self::DOCS, self::DOCS, 'API documentation', allowEmpty: true);

        return new ScaffoldOptions(
            withPwa: $withPwa,
            withDocker: $withDocker,
            formats: $formats,
            docs: $docs,
            withAdmin: $withAdmin,
        );
    }

    private function resolveWithAdmin(SymfonyStyle $io, InputInterface $input): bool
    {
        $option = $input->getOption('with-admin');
        if (null !== $option) {
            return (bool) $option;
        }

        if (!$input->isInteractive()) {
            return false;
        }

        $missing = $this->missingBinaries(['node', 'npm']);
        if ([] !== $missing) {
            $io->note(sprintf('Skipping admin prompt: %s not found in PATH.', implode(', ', $missing)));

            return false;
        }

        return (bool) $io->askQuestion(new ConfirmationQuestion('Include React-admin SPA?', false));
    }

    /**
     * @param array<string> $binaries
     *
     * @return array<string>
     */
    private function missingBinaries(array $binaries): array
    {
        $finder = new ExecutableFinder();

        return array_values(array_filter($binaries, static fn (string $b): bool => null === $finder->find($b)));
    }

    /**
     * @param array<string> $allowed
     * @param array<string> $defaults
     *
     * @return array<string>
     */
    private function resolveMulti(
        SymfonyStyle $io,
        InputInterface $input,
        string $option,
        array $allowed,
        array $defaults,
        string $label,
        bool $allowEmpty = false,
    ): array {
        $values = $input->getOption($option);
        if ([] !== $values) {
            if ($allowEmpty && \in_array('', $values, true)) {
                if ([] !== array_diff($values, [''])) {
                    throw new InvalidArgumentException(sprintf('Cannot combine empty --%s with other values.', $option));
                }

                return [];
            }

            $unknown = array_diff($values, $allowed);
            if ([] !== $unknown) {
                throw new InvalidArgumentException(sprintf('Unknown --%s value(s): %s. Allowed: %s.', $option, implode(', ', $unknown), implode(', ', $allowed)));
            }

            return array_values(array_unique($values));
        }

        if (!$input->isInteractive()) {
            return $defaults;
        }

        // SymfonyQuestionHelper renders a multiselect default by looking up
        // each comma-separated token as a key of the choices array. Pass the
        // numeric indices of the default values to satisfy that lookup.
        $defaultKeys = array_keys(array_intersect($allowed, $defaults));
        $question = new ChoiceQuestion(
            $label.' (comma-separated)',
            $allowed,
            implode(',', $defaultKeys),
        );
        $question->setMultiselect(true);
        if ($allowEmpty) {
            // ChoiceQuestion's constructor always installs a validator.
            $nativeValidator = $question->getValidator();
            \assert(null !== $nativeValidator);

            $question->setValidator(static function ($v) use ($nativeValidator): array {
                if (null === $v || '' === $v) {
                    return [];
                }

                return array_values((array) $nativeValidator($v));
            });
        }

        return (array) $io->askQuestion($question);
    }
}
