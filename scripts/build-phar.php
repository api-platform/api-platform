#!/usr/bin/env php
<?php

declare(strict_types=1);

// Build a self-contained Phar of the installer for static-php-cli's
// micro:combine step. Replaces the previous kevingh/box dependency.

if (ini_get('phar.readonly')) {
    fwrite(STDERR, "Run with: php -d phar.readonly=0 scripts/build-phar.php <output>\n");
    exit(1);
}

$root = \dirname(__DIR__);
$output = $argv[1] ?? $root.'/dist/api-platform.phar';
$alias = basename($output);

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
        $phar->addFromString($relative, file_get_contents($file->getPathname()));
    }
}
$phar->addFromString('composer.json', file_get_contents($root.'/composer.json'));

// Strip the shebang from bin/api-platform so the Phar stub can require it
// without leaking "#!/usr/bin/env php" to stdout at runtime.
$entry = file_get_contents($root.'/bin/api-platform');
$entry = preg_replace('/^#![^\n]*\n/', '', $entry);
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
