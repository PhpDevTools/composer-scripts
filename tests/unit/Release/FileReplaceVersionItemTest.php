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

namespace Gilbertsoft\Composer\Tests\Unit\Release;

use BadMethodCallException;
use Gilbertsoft\Composer\Release\FileReplaceVersionItem;
use Gilbertsoft\Composer\Tests\Unit\TestCase;
use Iterator;

/**
 * @covers \Gilbertsoft\Composer\Release\FileReplaceVersionItem
 */
final class FileReplaceVersionItemTest extends TestCase
{
    /**
     * @dataProvider fileProvider
     */
    public function testCreationAndGetters(
        string $filename,
        string $pattern,
        int $versionFormat,
        int $major,
        int $minor,
        int $patch,
        string $expectedReplacement
    ): void {
        $fileReplaceVersionItem = new FileReplaceVersionItem($filename, $pattern, $versionFormat);
        $fileReplaceVersionItem->setVersions($major, $minor, $patch);

        self::assertSame($filename, $fileReplaceVersionItem->getName());
        self::assertSame($pattern, $fileReplaceVersionItem->getPattern());
        self::assertSame($expectedReplacement, $fileReplaceVersionItem->getReplacement());
    }

    /**
     * @return Iterator<string, array{
     *   filename: string,
     *   pattern: string,
     *   versionFormat: int,
     *   major: int,
     *   minor: int,
     *   patch: int,
     *   expectedReplacement: string,
     * }>
     */
    public function fileProvider(): Iterator
    {
        yield 'major version' => [
            'filename' => 'filename1',
            'pattern' => '/\d+\.\d+\.\d+/',
            'versionFormat' => FileReplaceVersionItem::VERSION_MAJOR,
            'major' => 1,
            'minor' => 2,
            'patch' => 3,
            'expectedReplacement' => '1',
        ];
        yield 'minor version' => [
            'filename' => 'filename2',
            'pattern' => '/\d+\.\d+\.\d+/',
            'versionFormat' => FileReplaceVersionItem::VERSION_MINOR,
            'major' => 1,
            'minor' => 2,
            'patch' => 3,
            'expectedReplacement' => '1.2',
        ];
        yield 'patch version' => [
            'filename' => 'filename3',
            'pattern' => '/\d+\.\d+\.\d+/',
            'versionFormat' => FileReplaceVersionItem::VERSION_PATCH,
            'major' => 1,
            'minor' => 2,
            'patch' => 3,
            'expectedReplacement' => '1.2.3',
        ];
    }

    public function testGetReplacementThrowsOnMissingSetVersionsCall(): void
    {
        $fileReplaceVersionItem = new FileReplaceVersionItem('', '', 0);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionCode(1654889886);
        $this->expectExceptionMessage('Call to previous setVersions() is missing.');

        $fileReplaceVersionItem->getReplacement();
    }
}
