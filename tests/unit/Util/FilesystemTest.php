<?php

declare(strict_types=1);

/*
 * This file is part of the gilbertsoft/composer-scripts package.
 *
 * (c) Gilbertsoft LLC (gilbertsoft.org)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gilbertsoft\Composer\Tests\Unit\Util;

use Gilbertsoft\Composer\Tests\Unit\TestCase;
use Gilbertsoft\Composer\Util\Filesystem;
use Iterator;
use RuntimeException;
use UnexpectedValueException;

/**
 * @covers \Gilbertsoft\Composer\Util\Filesystem
 */
final class FilesystemTest extends TestCase
{
    private function getFileContents(string $filename): string
    {
        if (($content = file_get_contents($filename)) === false) {
            self::fail(sprintf('File "%s" not found.', $filename));
        }

        return $content;
    }

    public function testIsAbsolutePath(): void
    {
        $filesystem = new Filesystem();
        self::assertTrue($filesystem->isAbsolutePath('/absolute/path'));
        self::assertFalse($filesystem->isAbsolutePath('relative/path'));
    }

    /**
     * @dataProvider fileGetContentsProvider
     */
    public function testFileGetContents(string $filename): void
    {
        $filesystem = new Filesystem();

        self::assertStringEqualsFile(
            $filename,
            $filesystem->fileGetContents($filename)
        );
    }

    /**
     * @return Iterator<string, array{filename: string}>
     */
    public function fileGetContentsProvider(): Iterator
    {
        yield 'file in root' => [
            'filename' => 'composer.json',
        ];
        yield 'file in subfolder' => [
            'filename' => 'ddev/config.yaml',
        ];
    }

    public function testFileGetContentsThrowsOnInvalidFile(): void
    {
        $filesystem = new Filesystem();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(1654777708);
        $this->expectExceptionMessage(
            'Failed to read file "invalid".'
        );

        $filesystem->fileGetContents('invalid');
    }

    /**
     * @dataProvider filePutContentsProvider
     */
    public function testFilePutContents(string $filename, string $content, bool $exists): void
    {
        $filesystem = new Filesystem();

        self::assertFileDoesNotExist($filename);

        if ($exists) {
            $filesystem->filePutContents($filename, 'dummy-content');
            self::assertFileExists($filename);
        }

        $filesystem->filePutContents($filename, $content);

        self::assertFileExists($filename);
        self::assertStringEqualsFile($filename, $content);
    }

    /**
     * @return Iterator<string, array{filename: string, content: string, exists: bool}>
     */
    public function filePutContentsProvider(): Iterator
    {
        yield 'create file in root' => [
            'filename' => 'test.txt',
            'content' => 'test.txt content',
            'exists' => false,
        ];
        yield 'create file in subfolder' => [
            'filename' => 'ddev/test.txt',
            'content' => 'ddev/test.txt content',
            'exists' => false,
        ];
        yield 'overwrite file' => [
            'filename' => 'test.txt',
            'content' => 'test.txt new content',
            'exists' => true,
        ];
    }

    public function testFilePutContentsThrowsOnInvalidFile(): void
    {
        $filesystem = new Filesystem();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(1654777709);
        $this->expectExceptionMessage(
            'Failed to write file "invalid/invalid".'
        );

        $filesystem->filePutContents('invalid/invalid', '');
    }

    /**
     * @dataProvider fileReplaceContentsProvider
     */
    public function testFileReplaceContents(
        string $filename,
        string $pattern,
        string $replacement,
        string $needle
    ): void {
        $filesystem = new Filesystem();

        $filesystem->fileReplaceContents(
            $filename,
            $pattern,
            $replacement
        );

        self::assertStringContainsString(
            $needle,
            $this->getFileContents($filename)
        );

        // Test early return
        $filesystem->fileReplaceContents(
            $filename,
            $pattern,
            $replacement
        );

        self::assertStringContainsString(
            $needle,
            $this->getFileContents($filename)
        );
    }

    /**
     * @return Iterator<string, array{filename: string, pattern: string, replacement: string, needle: string}>
     */
    public function fileReplaceContentsProvider(): Iterator
    {
        yield '' => [
            'filename' => 'composer.json',
            'pattern' => '/("test-vendor\/test-package-required": "\^)\d+.\d+.\d+(")/',
            'replacement' => '${1}999.888.777${2}',
            'needle' => '"test-vendor/test-package-required": "^999.888.777"',
        ];
        /*
        yield '' => [
            'filename' => '',
            'pattern' => '',
            'replacement' => '',
            'needle' => '',
        ];
        yield '' => [
            'filename' => '',
            'pattern' => '',
            'replacement' => '',
            'needle' => '',
        ];*/
    }

    public function testFileReplaceContentsThrowsOnInvalidPattern(): void
    {
        $filesystem = new Filesystem();

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionCode(1654777711);
        $this->expectExceptionMessage(
            'Failed to replace content of "composer.json" using pattern "/.^*/" and replacement "test-replacement".'
        );

        $filesystem->fileReplaceContents(
            'composer.json',
            '/.^*/',
            'test-replacement'
        );
    }
}
