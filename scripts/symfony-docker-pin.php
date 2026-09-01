#!/usr/bin/env php
<?php

declare(strict_types=1);

// CLI front-end to SymfonyDockerPin, used by .github/workflows/symfony-docker-bump.yml.
//
//   ref            print the pinned symfony-docker commit
//   pin <sha>      repin the scaffold to <sha>
//   filter         read upstream paths on stdin, print those the scaffold copies

use ApiPlatform\Installer\Scaffold\SymfonyDockerPin;

require_once \dirname(__DIR__).'/vendor/autoload.php';

$scaffold = \dirname(__DIR__).'/src/Scaffold/SymfonyScaffold.php';

function fail(string $message): never
{
    fwrite(\STDERR, $message."\n");
    exit(1);
}

function read_source(string $path): string
{
    $contents = file_get_contents($path);
    if (false === $contents) {
        fail(sprintf('Could not read %s.', $path));
    }

    return $contents;
}

try {
    switch ($argv[1] ?? '') {
        case 'ref':
            fwrite(\STDOUT, SymfonyDockerPin::read(read_source($scaffold))."\n");
            break;

        case 'pin':
            $sha = $argv[2] ?? fail('Usage: symfony-docker-pin.php pin <sha>');
            $patched = SymfonyDockerPin::replace(read_source($scaffold), $sha);
            if (false === file_put_contents($scaffold, $patched)) {
                fail(sprintf('Could not write %s.', $scaffold));
            }
            break;

        case 'filter':
            $stdin = stream_get_contents(\STDIN);
            $paths = preg_split('/\R/', false === $stdin ? '' : trim($stdin), -1, \PREG_SPLIT_NO_EMPTY) ?: [];
            foreach (SymfonyDockerPin::copiedPaths($paths) as $path) {
                fwrite(\STDOUT, $path."\n");
            }
            break;

        default:
            fail('Usage: symfony-docker-pin.php <ref|pin <sha>|filter>');
    }
} catch (Throwable $e) {
    fail($e->getMessage());
}
