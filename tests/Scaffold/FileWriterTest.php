<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Tests\Scaffold;

use ApiPlatform\Installer\Scaffold\FileWriter;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class FileWriterTest extends TestCase
{
    public function testWritesContents(): void
    {
        $file = sys_get_temp_dir().'/file-writer-test-'.uniqid().'.txt';

        try {
            FileWriter::write($file, 'hello');

            $this->assertSame('hello', file_get_contents($file));
        } finally {
            @unlink($file);
        }
    }

    public function testThrowsWhenTargetIsNotWritable(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not write');

        FileWriter::write(sys_get_temp_dir().'/file-writer-missing-'.uniqid().'/file.txt', 'hello');
    }
}
