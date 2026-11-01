#!/usr/bin/env php
<?php

declare(strict_types=1);

// Build a self-contained Phar of the installer for static-php-cli's
// micro:combine step. Replaces the previous kevingh/box dependency.

if (ini_get('phar.readonly')) {
    fwrite(STDERR, "Run with: php -d phar.readonly=0 scripts/build-phar.php <output>\n");
    exit(1);
}

function read_file(string $path): string
{
    $contents = file_get_contents($path);
    if (false === $contents) {
        fwrite(STDERR, sprintf("Could not read %s\n", $path));
        exit(1);
    }

    return $contents;
}

$root = \dirname(__DIR__);
$output = $argv[1] ?? $root.'/dist/api-platform.phar';
$alias = basename($output);

// Version printed by `--version`: second argument (the release tag in CI),
// then the INSTALLER_VERSION environment variable, then `git describe --tags`.
// When none is available the placeholder stays and the binary reports "dev".
// The value ends up in a PHP string literal, so only plain version strings are
// accepted: an explicit invalid version aborts the build, an unusable
// `git describe` output falls back to "dev".
const VERSION_PATTERN = '/^v?\d+\.\d+\.\d+(-[0-9A-Za-z.-]+)?$/D';

$version = $argv[2] ?? (getenv('INSTALLER_VERSION') ?: '');
if ('' !== $version && !preg_match(VERSION_PATTERN, $version)) {
    fwrite(STDERR, sprintf("Invalid version \"%s\": expected something like v1.2.3 or 1.2.3-beta.1\n", addcslashes($version, "\0..\37\177..\377")));
    exit(1);
}
if ('' === $version) {
    $version = trim((string) shell_exec('git -C '.escapeshellarg($root).' describe --tags 2>/dev/null'));
    if ('' !== $version && !preg_match(VERSION_PATTERN, $version)) {
        fwrite(STDERR, "Ignoring `git describe` output that is not a version, the binary will report \"dev\"\n");
        $version = '';
    }
}

@unlink($output);
@mkdir(\dirname($output), 0o755, true);

$phar = new Phar($output, 0, $alias);
$phar->setSignatureAlgorithm(Phar::SHA256);
$phar->startBuffering();

$includeDirs = ['src', 'templates', 'vendor'];
foreach ($includeDirs as $dir) {
    $absolute = $root.'/'.$dir;
    if (!is_dir($absolute)) {
        continue;
    }
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($absolute, FilesystemIterator::SKIP_DOTS),
    );
    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }
        $relative = ltrim(substr($file->getPathname(), \strlen($root)), '/');
        $contents = read_file($file->getPathname());
        if ('' !== $version && 'src/InstallerCommand.php' === $relative) {
            $contents = str_replace("'@package_version@'", var_export($version, true), $contents, $count);
            if (1 !== $count) {
                fwrite(STDERR, "Could not find the version placeholder in src/InstallerCommand.php\n");
                exit(1);
            }
        }
        $phar->addFromString($relative, $contents);
    }
}
$phar->addFromString('composer.json', read_file($root.'/composer.json'));

// Strip the shebang from bin/api-platform so the Phar stub can require it
// without leaking "#!/usr/bin/env php" to stdout at runtime.
$entry = preg_replace('/^#![^\n]*\n/', '', read_file($root.'/bin/api-platform'));
$phar->addFromString('bin/api-platform', $entry);

$phar->setStub(<<<STUB
#!/usr/bin/env php
<?php
Phar::mapPhar('{$alias}');
require 'phar://{$alias}/bin/api-platform';
__HALT_COMPILER();
STUB);

$phar->stopBuffering();
chmod($output, 0o755);

fwrite(\STDOUT, sprintf("Built %s (%d files)\n", $output, $phar->count()));
